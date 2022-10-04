<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AnnualReport;

use DB;

class ReportController extends Controller
{
    // Annual Reports
    public function arindex(){
        return AnnualReport::orderBy('year', 'desc')->get();
    }

    public function arstore(Request $request){
        DB::beginTransaction();
        try {
            $annualreport = AnnualReport::where('year', $request['year'])->get();
            if(sizeof($annualreport) > 0){
                DB::rollback();
                return ['message' => 'Annual Report for year '.$request['year'].' already exists', 'errors' => 'Annual Report already exists'];
            }
            if($request->hasFile('thumbnail') && $request->hasFile('pdf')){
                // Set files
                $thumbnail = $request->file('thumbnail');
                $pdf = $request->file('pdf');
                // Get filenames
                $thumbnail_filename = time().'_'.$thumbnail->getClientOriginalName();
                $pdf_filename = time().'_'.$pdf->getClientOriginalName();
                // Check file extensions
                if($this->checkFileExtension($thumbnail_filename, 'image')){
                    return ['message' => 'Thumbnail uploaded is not an image file', 'errors' => 'Thumbnail error'];
                }
                if($this->checkFileExtension($pdf_filename, 'pdf')){
                    return ['message' => 'File uploaded is not a PDF file', 'errors' => 'PDF error'];
                }
                // Save info to database
                $annualreport = new AnnualReport();
                $annualreport->fill([
                    'year' => $request['year'],
                    'description' => $request['description'],
                    'thumbnail' => $thumbnail_filename,
                    'pdf_file' => $pdf_filename,
                ])->save();

                // Save files
                $thumbnail->move(public_path('annual/thumbnails'), $thumbnail_filename);
                $pdf->move(public_path('annual/pdfs'), $pdf_filename);
                
                DB::commit();
                return response(['message' => 'Annual Report saved!', 'reports' => $this->arindex()]);
            }
        }
        catch (\Exception $e){
            DB::rollback();
            return ['message' => 'Something went wrong' , 'errors' => $e->getMessage()];
        }
    }

    public function arupdate(Request $request, $id){
        DB::beginTransaction();
        try {
            $annualreport = AnnualReport::where('year', $request['year'])->get();
            if(sizeof($annualreport) > 0 && $annualreport[0]->id != $id){
                DB::rollback();
                return ['message' => 'Annual Report for year '.$request['year'].' already exists', 'errors' => 'Annual Report already exists'];
            }
            $annualreport = AnnualReport::findOrFail($id);
            $annualreport->year = $request['year'];
            $annualreport->description = $request['description'];

            $thumbnail = $this->saveFile($request, 'thumbnail', $annualreport->thumbnail);
            if(array_key_exists('errors',$thumbnail)){
                DB::rollback();
                return ['message' => 'Something went wrong', 'errors' => $thumbnail['errors']];
            }
            $annualreport->thumbnail = $thumbnail['filename'] != '' ? $thumbnail['filename'] : $annualreport->thumbnail;

            $pdf = $this->saveFile($request, 'pdf', $annualreport->pdf_file);
            if(array_key_exists('errors',$pdf)){
                DB::rollback();
                return ['message' => 'Something went wrong', 'errors' => $thumbnail['errors']];
            }
            $annualreport->pdf_file = $pdf['filename'] != '' ? $pdf['filename'] : $annualreport->pdf_file;

            $annualreport->save();

            DB::commit();
            return response(['message' => 'Annual Report saved!', 'reports' => $this->arindex()]);
        }
        catch (\Exception $e){
            DB::rollback();
            return ['message' => 'Something went wrong' , 'errors' => $e->getMessage()];
        }
    }

    public function ardelete($id){
        $annualreport = AnnualReport::findOrFail($id);
        $annualreport->delete();
        unlink('annual/thumbnails/'.$annualreport->thumbnail);
        unlink('annual/pdfs/'.$annualreport->pdf_file);

        return ['message' => 'Annual Report deleted!', 'reports' => $this->arindex()];
    }

    private function checkFileExtension($filename, $type){
        $array = $type == 'pdf' ? ['pdf'] : ['png', 'jpg', 'jpeg', 'gif'];
        $filenameArray = explode('.',$filename);
        $fileExtension = end($filenameArray);
        return !in_array($fileExtension, $array);
    }

    private function saveFile($request, $type, $old = ''){
        try {
            if($request->hasFile($type)){
                $file = $request->file($type);
                $filename = time().'_'.$file->getClientOriginalName();
                $extensionOf = $type == 'thumbnail' ? 'image' : 'pdf';
                if($this->checkFileExtension($filename, $extensionOf)){
                    $message = $type == 'thumbnail' ? 'Thumbnail uploaded is not an image file' 
                                : 'File uploaded is not a PDF file';
                    return ['message' => $message, 'errors' => 'Thumbnail error'];
                }
                $path = $type == 'thumbnail' ? 'thumbnails' : 'pdfs';
                $file->move(public_path('annual/'.$path), $filename);

                if($old != ''){
                    $path = 'annual/'.$path.'/'.$old;
                    if(file_exists($path)){
                        unlink($path);
                    }
                }

                return ['message' => 'File saved!', 'filename' => $filename];
            }
            return ['filename' => ''];
        }
        catch (\Exception $e){
            return ['message' => 'Something went wrong' , 'errors' => $e->getMessage()];
        }
    }

}
