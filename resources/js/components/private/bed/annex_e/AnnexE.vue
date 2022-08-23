<template>
    <div class="px-3 py-4">
        <button class="btn btn-sm btn-light float-start" @click="this.$router.back()"><i class="fas fa-arrow-left"></i> Back</button>
        <h2 class="text-center">Annex E</h2>
        <small>Planning Workshop <span v-if="!loading">{{workshop.date}}</span><span v-else>Loading date <i class="fas fa-spinner fa-spin"></i></span></small><hr>
        <template v-if="!loading">
            <div class="row flex-row-reverse" v-if="!formshow && !detailshow">
                <div class="col-sm-3">
                    <div class="d-flex justify-content-end mb-3">
                        <button style="width: 120px" class="btn shadow-none" :class="!editmode ? 'btn-success' : 'btn-primary'" @click="editmode = !editmode">{{editmode ? 'View' : 'Edit'}} Mode</button>
                    </div>
                    <div class="card shadow mb-3">
                        <div class="card-body">
                            <div class="form-floating mb-3">
                                <select class="form-control" id="Status" v-model="displaystatus">
                                    <option value="New">New</option>
                                    <option value="Draft">Draft</option>
                                    <option value="For Review">For Review</option>
                                    <option value="For Approval">For Approval</option>
                                    <option value="Approved">Approved</option>
                                    <option value="Submitted">Submitted</option>
                                </select>
                                <label for="Status">Status</label>
                            </div>
                            <div class="form-floating mb-3">
                                <select class="form-control" id="displayType" v-model="displaytype" @change="displaytypeChange()">
                                    <option value="Program">Program</option>
                                    <option value="Division">Division</option>
                                </select>
                                <label for="displayType">Type</label>
                            </div>
                            <template v-if="displaytype == 'Program'">
                                <div class="form-floating mb-3">
                                    <select class="form-control" id="Program" v-model="disProg.program_id" @change="progChange()">
                                        <option :value="0">Any Program</option>
                                        <option :value="program.id" v-for="program in programs" :key="'program_'+program.id">{{program.title}}</option>
                                    </select>
                                    <label for="Program">Program</label>
                                </div>
                                <div class="form-floating mb-3" v-if="subprograms.length > 0">
                                    <select class="form-control" id="SubProgram" v-model="disProg.subprogram_id" @change="subpChange()">
                                        <option :value="0">Any Sub-Program</option>
                                        <option :value="subprogram.id" v-for="subprogram in subprograms" :key="'subprogram_'+subprogram.id">{{(disProg.program_id != 2) ? subprogram.title_short : subprogram.title}}</option>
                                    </select>
                                    <label for="SubProgram">Sub-Program</label>
                                </div>
                                <div class="form-floating mb-3" v-if="clusters.length > 0">
                                    <select class="form-control" id="Cluster" v-model="disProg.cluster_id">
                                        <option :value="0">Any Cluster</option>
                                        <option :value="cluster.id" v-for="cluster in clusters" :key="'cluster_'+cluster.id">{{cluster.title}}</option>
                                    </select>
                                    <label for="Cluster">Cluster</label>
                                </div>
                            </template>
                            <template v-if="displaytype == 'Division'">
                                <div class="form-floating mb-3">
                                    <select class="form-control" id="Division" v-model="disDiv.division_id" @change="divChange()">
                                        <option :value="0">Any Division</option>
                                        <option :value="division.id" v-for="division in divisions" :key="'division_'+division.id">{{division.name}}</option>
                                    </select>
                                    <label for="Division">Division</label>
                                </div>
                                <div class="form-floating mb-3" v-if="units.length > 0">
                                    <select class="form-control" id="Unit" v-model="disDiv.unit_id" @change="unitChange()">
                                        <option :value="0">Any Unit</option>
                                        <option :value="unit.id" v-for="unit in units" :key="'unit_'+unit.id">{{unit.name}}</option>
                                    </select>
                                    <label for="Unit">Unit</label>
                                </div>
                                <div class="form-floating mb-3" v-if="subunits.length > 0">
                                    <select class="form-control" id="SubUnit" v-model="disDiv.subunit_id">
                                        <option :value="0">Any Sub-Unit</option>
                                        <option :value="subunit.id" v-for="subunit in subunits" :key="'subunit_'+subunit.id">{{subunit.name}}</option>
                                    </select>
                                    <label for="SubUnit">Sub-Unit</label>
                                </div>
                            </template>
                            <div class="d-flex justify-content-end">
                                <button class="btn btn-sm btn-primary shadow-none" @click="syncRecords()">{{syncing ? 'Syncing...' : 'Sync'}} <i class="far fa-sync-alt" :class="syncing ? 'fa-spin' : ''"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-9 mb-3">
                    <div class="card shadow overflow-auto" style="height: 76vh" v-if="!editmode">
                        <div class="card-body">
                            <div class="card-overlay" v-if="syncing"><h1><i class="fas fa-spinner fa-spin fa-5x"></i></h1> </div>
                            <h6 class="text-end fw-bold">Annex E</h6>
                            <h6 class="text-center mb-3 fw-bold">CY {{workshop.year}} PHYSICAL PLAN </h6>
                            <EmptyTable />

                            <span class="position-absolute bottom-0"><small>Empty Data Set</small></span>
                        </div>
                    </div>
                    <div v-else>
                        <div class="d-flex justify-content-end mb-2">
                            <button class="btn btn-sm btn-success me-1" v-if="displaysyncstatus != 'New'">Batch Status Change</button>
                            <button class="btn btn-sm btn-success"><i class="fas fa-plus"></i> New</button>
                        </div>
                        <div class="table-responsive" style="height: 70vh">
                            <table class="table table-sm table-bordered table-hover">
                                <thead class="text-center">
                                    <tr>
                                        <th style="width: 40%">Program/Project</th>
                                        <th style="width: 40%">Performance Indicators (PIs)</th>
                                        <th style="width: 20%">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <template v-if="!syncing">
                                        <template v-for="items, header in annexes" :key="'annexe_'+header">
                                            <tr><th class="bg-success text-white" colspan="3">{{header}}</th></tr>
                                            <template v-for="item in items" :key="'annexe_'+item.id">
                                                <tr>
                                                    <td><div class="ms-2 fw-bold">{{item.project.title}}</div></td>
                                                    <td class="p-0" style="height: 1px;">
                                                        <table class="table table-sm h-100 m-0">
                                                            <tr v-for="indicator, key in item.indicators" :key="'indicator_'+indicator.id">
                                                                <td :class="(item.indicators.length - 1) != key ? 'border-bottom' : ''">{{indicator.description}}</td>
                                                            </tr>
                                                        </table>
                                                    </td>
                                                    <td class="text-center" :rowspan="item.subs.length + 1">
                                                        <!-- <button style="width: 48px"  class="shadow-none btn btn-sm btn-primary me-1 mb-1"><i class="far fa-pencil-alt"></i></button> -->
                                                        <button style="width: 100px" class="shadow-none btn btn-sm btn-outline-secondary me-1 mb-1" data-bs-toggle="modal" data-bs-target="#form" @click="editForm(item, 'indicator')"><i class="far fa-pencil-alt"></i> Indicators</button><br>
                                                        <button style="width: 100px" class="shadow-none btn btn-sm btn-outline-secondary me-1 mb-1" @click="editForm(item, 'details')"><i class="far fa-pencil-alt"></i> Details</button><br>
                                                        <button style="width: 100px"  class="shadow-none btn btn-sm btn-danger me-1 mb-1"><i class="far fa-trash-alt"></i> Remove</button>
                                                    </td>
                                                </tr>
                                                <tr v-for="sub in item.subs" :key="'sub_'+sub.id">
                                                    <td><div class="ms-4">{{(sub.subproject_id !== null) ? sub.subproject.title : (sub.temp_title == 'ms' ? 'MS' : sub.temp_title == 'phd' ? 'PhD' : sub.temp_title)}}</div></td>
                                                    
                                                    <td class="p-0" style="height: 1px;">
                                                        <table class="table table-sm h-100 m-0">
                                                            <tr v-for="indicator, key in sub.indicators" :key="'indicator_'+indicator.id">
                                                                <td :class="(sub.indicators.length - 1) != key ? 'border-bottom' : ''"><div class="ms-2">{{indicator.description}}</div></td>
                                                            </tr>
                                                        </table>
                                                    </td>
                                                </tr>
                                            </template>
                                        </template>
                                    </template>
                                    <tr v-else><td colspan="3" class="text-center p-4"><h3>Syncing <i class="fas fa-sync fa-spin"></i> </h3></td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div v-if="detailshow">
                <div class="d-flex justify-content-between mb-3">
                    <button class="btn btn-sm btn-danger" @click="detailshow = false"><i class="fas fa-times"></i> Cancel</button>
                    <div>
                        <button class="btn btn-sm btn-outline-secondary shadow-none me-1" v-if="form.program_id == 1" data-bs-toggle="modal" data-bs-target="#form" @click="showComputeForm()"><i class="far fa-cogs"></i></button>
                        <button class="btn btn-sm btn-primary" @click="submitForm()"><i class="fas fa-save"></i> Save changes</button>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm table-bordered">
                        <thead class="align-middle text-center">
                            <tr>
                                <th rowspan="3">Program / Project</th>
                                <th rowspan="3">Performance Indicators (PIs)</th>
                                <th colspan="2">Previous Year Acccomplishments <br> CY {{parseInt(workshop.year)}}</th>
                                <th rowspan="3">CY {{parseInt(workshop.year) + 1}} <br> Physical Targets</th>
                                <th colspan="4">CY {{parseInt(workshop.year) + 1}} Quarterly Physical Targets</th>
                            </tr>
                            <tr>
                                <th>Actual</th>
                                <th>Estimate</th>
                                <th rowspan="2">1st</th>
                                <th rowspan="2">2nd</th>
                                <th rowspan="2">3rd</th>
                                <th rowspan="2">4th</th>
                            </tr>
                            <tr>
                                <th>Jan 1 - Sep 30</th>
                                <th>Oct 1 - Dec 30</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td :rowspan="form.indicators.length + 1">{{form.project_title}}</td>
                                <template v-if="form.indicators.length > 0">
                                    <td style="height: 1px" class="p-0 align-middle" v-for="col in indcols" :key="col">
                                        <!-- Program 1: S&T Scholarship Program -->
                                        <template v-if="form.program_id == 1">
                                            <p :class="form.indicators[0].id == totalSelectedId ? 'fw-bold' : ''" class="px-2 py-1 m-0" v-if="col == 'description'">{{form.indicators[0][col]}}</p>
                                            <template v-else>
                                                <template v-if="form.indicators[0].description != 'Sub-Total'">
                                                    <p v-if="form.indicators[0].id == totalSelectedId" class="px-2 py-1 m-0 text-end fw-bold">{{form.indicators[0][col] = totalIndicator(col)}}</p>
                                                    <input  v-else type="text" v-model="form.indicators[0][col]" v-money="money" class="form-control indicator-input">
                                                </template>
                                                <template v-else>
                                                    <p class="px-2 py-1 m-0 text-end fw-bold">{{subtotalIndicator(col)}}</p>
                                                </template>
                                            </template>
                                        </template>
                                        <!-- Program 2: S&T Educational Development Program -->
                                        <template v-else>
                                            <p class="px-2 py-1 m-0" v-if="col == 'description'">{{form.indicators[0][col]}}</p>
                                            <input v-else-if="col == 'actual' || col == 'estimate'" type="text" v-model="form.indicators[0][col]" v-money="money" class="form-control indicator-input">
                                            <p class="px-2 py-1 m-0 text-end" style="cursor: pointer;" data-bs-toggle="modal" data-bs-target="#form" @click="showIndicatorBreakdown(0)" v-else>{{form.indicators[0][col] = formatNumber(totalIndicatorBreakdown(0, col))}}</p>
                                        </template>
                                    </td>
                                </template>
                                <template v-else>
                                    <!-- No indicators.jpeg -->
                                    <td v-for="col in indcols" :key="col+'_empty_'+sub.id"></td>
                                </template>
                            </tr>
                            <tr v-for="indicator, key in form.indicators" :key="'indicator_'+key">
                                <template v-if="key != 0">
                                    <td style="height: 1px" class="p-0 align-middle" v-for="col in indcols" :key="col">
                                        <!-- Program 1: S&T Scholarship Program -->
                                        <template v-if="form.program_id == 1">
                                            <p :class="indicator.id == totalSelectedId ? 'fw-bold' : ''" class="px-2 py-1 m-0" v-if="col == 'description'">{{indicator[col]}}</p>
                                            <template v-else>
                                                <template v-if="indicator.description != 'Sub-Total'">
                                                    <p v-if="indicator.id == totalSelectedId" class="px-2 py-1 m-0 text-end fw-bold">{{indicator[col] = totalIndicator(col)}}</p>
                                                    <input v-else type="text" v-model="indicator[col]" v-money="money" class="form-control indicator-input">
                                                </template>
                                                <template v-else>
                                                    <p class="px-2 py-1 m-0 text-end fw-bold">{{subtotalIndicator(col)}}</p>
                                                </template>
                                            </template>
                                        </template>
                                        <!-- Program 2: S&T Educational Development Program -->
                                        <template v-else>
                                            <p class="px-2 py-1 m-0" v-if="col == 'description'">{{indicator[col]}}</p>
                                            <input v-else-if="col == 'actual' || col == 'estimate'" type="text" v-model="indicator[col]" v-money="money" class="form-control indicator-input">
                                            <p class="px-2 py-1 m-0 text-end" style="cursor: pointer;" data-bs-toggle="modal" data-bs-target="#form" @click="showIndicatorBreakdown(key)" v-else>{{indicator[col] = formatNumber(totalIndicatorBreakdown(key, col))}}</p>
                                        </template>
                                    </td>
                                </template>
                            </tr>
                            <template v-for="sub, key in form.subs" :key="'sub_'+key">
                                <tr>
                                    <td :rowspan="sub.indicators.length+1">{{sub.title}}</td>
                                    <template v-if="sub.indicators.length > 0">
                                        <td style="height: 1px" class="p-0 align-middle" v-for="col in indcols" :key="col">
                                            <!-- Program 1: S&T Scholarship Program -->
                                            <template v-if="form.program_id == 1">
                                                <p class="px-2 py-1 m-0" v-if="col == 'description'">{{sub.indicators[0][col]}}</p>
                                                <template v-else>
                                                    <template v-if="sub.indicators[0].description != 'Sub-Total'">
                                                        <p v-if="sub.indicators[0].id == totalSelectedId" class="px-2 py-1 m-0 text-end fw-bold">{{sub.indicators[0][col] = totalIndicator(col)}}</p>
                                                        <input v-else type="text" v-model="sub.indicators[0][col]" v-money="money" class="form-control indicator-input">
                                                    </template>
                                                    <template v-else>
                                                        <p class="px-2 py-1 m-0 text-end fw-bold">{{subtotalIndicator(col, sub.id)}}</p>
                                                    </template>
                                                </template>
                                            </template>
                                            <!-- Program 2: S&T Educational Development Program -->
                                            <template v-else>
                                                <p class="px-2 py-1 m-0" v-if="col == 'description'">{{sub.indicators[0][col]}}</p>
                                                <input v-else-if="col == 'actual' || col == 'estimate'" type="text" v-model="sub.indicators[0][col]" v-money="money" class="form-control indicator-input">
                                                <p class="px-2 py-1 m-0 text-end" style="cursor: pointer;" data-bs-toggle="modal" data-bs-target="#form" @click="showIndicatorBreakdown(0)" v-else>{{sub.indicators[0][col] = formatNumber(totalIndicatorBreakdown(0, col))}}</p>
                                            </template>
                                        </td>
                                    </template>
                                    <template v-else>
                                        <!-- No indicators.jpeg -->
                                        <td v-for="col in indcols" :key="col+'_empty_'+sub.id"></td>
                                    </template>
                                </tr>
                                                        
                                <tr v-for="indicator, key in sub.indicators" :key="'indicator_'+key">
                                    <template v-if="key != 0">
                                        <td style="height: 1px" class="p-0 align-middle" v-for="col in indcols" :key="col">
                                            <!-- Program 1: S&T Scholarship Program -->
                                            <template v-if="form.program_id == 1">
                                                <p class="px-2 py-1 m-0" v-if="col == 'description'">{{indicator[col]}}</p>
                                                <template v-else>
                                                    <template v-if="indicator.description != 'Sub-Total'">
                                                        <p v-if="indicator.id == totalSelectedId" class="px-2 py-1 m-0 text-end fw-bold">{{indicator[col] = totalIndicator(col)}}</p>
                                                        <input v-else type="text" v-model="indicator[col]" v-money="money" class="form-control indicator-input">
                                                    </template>
                                                    <template v-else>
                                                        <p class="px-2 py-1 m-0 text-end fw-bold">{{subtotalIndicator(col, sub.id)}}</p>
                                                    </template>
                                                </template>
                                            </template>
                                            <!-- Program 2: S&T Educational Development Program -->
                                            <template v-else>
                                                <p class="px-2 py-1 m-0" v-if="col == 'description'">{{indicator[col]}}</p>
                                                <input v-else-if="col == 'actual' || col == 'estimate'" type="text" v-model="indicator[col]" v-money="money" class="form-control indicator-input">
                                                <p class="px-2 py-1 m-0 text-end" style="cursor: pointer;" data-bs-toggle="modal" data-bs-target="#form" @click="showIndicatorBreakdown(key)" v-else>{{indicator[col] = formatNumber(totalIndicatorBreakdown(key, col))}}</p>
                                            </template>
                                        </td>
                                    </template>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
        </template>
        <h1 v-else class="text-center p-5"><i class="fas fa-spinner fa-spin fa-5x"></i></h1>
    </div>
    <!-- Modal -->
    <div class="modal fade" id="form" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered" :class="!breakdownform ? 'modal-lg' : ''">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" v-if="breakdownform">{{form.indicators[breakdownkey].description}}</h5>
                <h5 class="modal-title" v-if="computeform"> <span>{{form.project_title}} Performance Indicators</span></h5>
                <h5 class="modal-title" v-if="indicatorshow"> <span>Performance Indicators</span></h5>
                <button type="button" ref="Close" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0" v-if="breakdownform">
                <table class="table table-sm table-bordered m-0 align-middle">
                    <thead class="text-center">
                        <tr>
                            <th style="width: 68px">Quarter</th>
                            <th colspan="3">Monthly Physical Targets</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="breakdown in form.indicators[breakdownkey].breakdowns" :key="'quarter_'+breakdown.quarter">
                            <td class="text-center fw-bold">{{formatQtr(breakdown.quarter)}}</td>
                            <td class="p-0" v-for="month in breakdownmonths" :key="'quarter_'+breakdown.quarter+'_'+month">
                                <div class="form-floating">
                                    <input type="text" class="form-control indicator-input" :id="formatMonth(breakdown.quarter, month)" v-money="money" v-model="breakdown[month]" :placeholder="formatMonth(breakdown.quarter, month)">
                                    <label :for="formatMonth(breakdown.quarter, month)">{{formatMonth(breakdown.quarter, month)}}</label>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="modal-body p-0" v-if="computeform">
                <button class="btn btn-sm btn-light shadow-none px-5" @click="totalSelectedId = 0"><i class="fas fa-undo"></i> Reset Selected Total</button>
                <table class="table table-sm table-bordered m-0">
                    <thead class="text-center">
                        <tr>
                            <th style="width: 40%">Program / Project</th>
                            <th colspan="2">Performance Indicators (PIs)</th>
                            <th style="width: 140px">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td :rowspan="form.indicators.length+1">{{form.project_title}}</td>
                            <template v-if="form.indicators.length > 0">
                                <td>{{form.indicators[0].description}}</td>
                                <td class="text-center">
                                    <div class="form-check">
                                        <input class="form-check-input shadow-none" type="radio" v-model="totalSelectedId" :value="form.indicators[0].id" :id="form.indicators[0].description+'_0'">
                                        <label class="form-check-label" :for="form.indicators[0].description+'_0'">
                                            Set as Total
                                        </label>
                                    </div>
                                </td>
                                <td :rowspan="form.indicators.length+1" class="align-middle">
                                    <button class="btn btn-sm btn-danger" v-if="withSubtotal(form.indicators)" @click="removeSubtotal()">Remove Sub-Total</button>
                                    <button class="btn btn-sm btn-success" v-else @click="appendSubtotal()">Append Sub-Total</button>
                                </td>
                            </template>
                        </tr>
                        <tr v-for="indicator, key in form.indicators" :key="'indicator_calc_'+key">
                            <template v-if="key != 0">
                                <td>{{indicator.description}}</td>
                                <td class="text-center">
                                    <div class="form-check">
                                        <input class="form-check-input shadow-none" type="radio" v-model="totalSelectedId" :value="indicator.id" :id="indicator.description+'_'+key">
                                        <label class="form-check-label" :for="indicator.description+'_'+key">
                                            Set as Total
                                        </label>
                                    </div>
                                </td>
                            </template>
                        </tr>
                        <template v-for="sub, key in form.subs" :key="'sub_opt'+key">
                            <tr>
                                <td :rowspan="sub.indicators.length+1">{{sub.title}}</td>
                                <template v-if="sub.indicators.length > 0">
                                    <td colspan="2">{{sub.indicators[0].description}}</td>
                                    <td :rowspan="sub.indicators.length+1" class="align-middle">
                                        <button class="btn btn-sm btn-danger" v-if="withSubtotal(sub.indicators)" @click="removeSubtotal(sub.id)">Remove Sub-Total</button>
                                        <button class="btn btn-sm btn-success" v-else @click="appendSubtotal(sub.id)">Append Sub-Total</button>
                                    </td>
                                </template>
                            </tr>
                            <tr v-for="indicator, key in sub.indicators" :key="'indicator_calc_'+key">
                                <template v-if="key != 0">
                                    <td colspan="2">{{indicator.description}}</td>
                                </template>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
            <div class="modal-body p-0 overflow-auto" style="height: 50vh;" v-if="indicatorshow">
                <table class="table table-sm table-bordered m-0">
                    <thead class="text-center">
                        <tr>
                            <th style="width: 40%">Program/Project</th>
                            <th>Performance Indicators (PIs)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                           <td :rowspan="form.indicators.length+1">
                                <div class="d-flex justify-content-between">
                                    <div>{{form.project_title}}</div>
                                    <div style="min-width: 90px; cursor: pointer;" @click="addIndicator()" class="text-end"><i style="cursor: pointer;" class="fas fa-plus"></i> Indicator</div>
                                </div>
                            </td>
                           <template v-if="form.indicators.length>0">
                                <td :class="!form.indicators[0].common ? 'p-0' : ''">
                                    <span v-if="form.indicators[0].common">{{form.indicators[0].description}}</span>
                                    <div v-else class="position-relative">
                                        <button @click="removeIndicator(form.indicators[0])" class="btn btn-sm btn-outline-danger position-absolute end-0 h-100 rounded-0 border-0 shadow-none"><i class="fas fa-times"></i></button>
                                        <input type="text" v-model="form.indicators[0].description" class="form-control rounded-0 border-0 shadow-none" placeholder="Indicator description...">
                                    </div>
                                </td>
                           </template>
                        </tr>
                        <tr v-for="indicator, key in form.indicators" :key="'indicator_'+key">
                            <template v-if="key != 0">
                            <td :class="!indicator.common ? 'p-0' : ''">
                                <span v-if="indicator.common">{{indicator.description}}</span>
                                <div v-else class="position-relative">
                                    <button @click="removeIndicator(indicator)" class="btn btn-sm btn-outline-danger position-absolute end-0 h-100 rounded-0 border-0 shadow-none"><i class="fas fa-times"></i></button>
                                    <input v-model="indicator.description" type="text" class="form-control rounded-0 border-0 shadow-none" placeholder="Indicator description...">
                                </div>
                            </td>
                            </template>
                        </tr>
                        <template v-for="sub, key in form.subs" :key="key">
                            <tr>
                                <td :rowspan="sub.indicators.length+1">
                                    <div class="d-flex justify-content-between">
                                        <div>{{sub.title}}</div>
                                        <div style="min-width: 90px; cursor: pointer;" @click="addIndicator(sub.id)" class="text-end"><i style="cursor: pointer;" class="fas fa-plus"></i> Indicator</div>
                                    </div>
                                </td>
                                <template v-if="sub.indicators.length>0">
                                    <td :class="!sub.indicators[0].common ? 'p-0' : ''">
                                        <span v-if="sub.indicators[0].common">{{sub.indicators[0].description}}</span>
                                        <div v-else class="position-relative">
                                            <button @click="removeIndicator(sub.indicators[0], sub.id)" class="btn btn-sm btn-outline-danger position-absolute end-0 h-100 rounded-0 border-0 shadow-none"><i class="fas fa-times"></i></button>
                                            <input v-model="sub.indicators[0].description" type="text" class="form-control rounded-0 border-0 shadow-none" placeholder="Indicator description...">
                                        </div>
                                    </td>
                                </template>
                            </tr>
                            <tr v-for="indicator, key in sub.indicators" :key="'indicator_'+key">
                                <template v-if="key != 0">
                                    <td :class="!indicator.common ? 'p-0' : ''">
                                        <span v-if="indicator.common">{{indicator.description}}</span>
                                        <div v-else class="position-relative">
                                            <button @click="removeIndicator(indicator, sub.id)" class="btn btn-sm btn-outline-danger position-absolute end-0 h-100 rounded-0 border-0 shadow-none"><i class="fas fa-times"></i></button>
                                            <input v-model="indicator.description" type="text" class="form-control rounded-0 border-0 shadow-none" placeholder="Indicator description...">
                                        </div>
                                    </td>
                                </template>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button @click="submitForm()" type="button" v-if="indicatorshow" class="btn btn-primary rounded-pill" style="min-width: 100px">Save</button>
                <button type="button" v-else :class="computeform ? 'btn-primary' : 'btn-secondary'" class="btn rounded-pill" data-bs-dismiss="modal" style="min-width: 100px">{{ computeform ? 'Save' : 'Done'}}</button>
            </div>
            </div>
        </div>
    </div>
</template>
<script>
import EmptyTable from './EmptyTable.vue'
import Display from './Display.vue'
import Form from './Form.vue'
import { mapActions, mapGetters } from 'vuex'
export default {
    name: 'AnnexE',
    components: { EmptyTable, Display, Form },
    data(){
        return {
            cost: 12345,
            displaytype: 'Program',
            displaystatus: 'New',
            displaysyncstatus: 'New',
            disProg: {
                program_id: 0,
                subprogram_id: 0,
                cluster_id: 0
            },
            subprograms: [],
            clusters: [],
            disDiv: {
                division_id: 0,
                unit_id: 0,
                subunit_id: 0
            },
            units: [],
            subunits: [],
            syncing: false,
            loading: true,
            editmode: false,
            
            // form
            formshow: false,
            detailshow: false,
            form: {
                id: '',
                program_id: 0,
                project_id: '',
                project_title: '',
                indicators: [],
                subs: [],
                formtype: ''
            },
            // indicator columns
            indcols: ['description', 'actual', 'estimate', 'physical_targets', 'first', 'second', 'third', 'fourth'],
            breakdownform: false,
            breakdownmonths: ['first','second','third'],
            breakdownkey: 0,
            computeform: false,
            totalSelectedId: 0,
            money: {
                decimal: '.',
                thousands: ',',
                prefix: '',
                suffix: '',
                precision: 0,
                masked: false /* doesn't work with directive */
            },
            indicatorshow: false
        }
    },
    methods: {
        ...mapActions('workshop', ['fetchWorkshop']),
        ...mapActions('annexe', ['fetchAnnexEs', 'saveAnnexE']),
        ...mapActions('program', ['fetchPrograms']),
        ...mapActions('division', ['fetchDivisions']),
        displaytypeChange(){
            this.disProg.program_id = 0
            this.disProg.subprogram_id = 0
            this.disProg.cluster_id = 0
            this.subprograms = []
            this.clusters = []

            this.disDiv.division_id = 0
            this.disDiv.unit_id = 0
            this.disDiv.subunit_id = 0
            this.units = []
            this.subunits = []
        },
        progChange(){
            this.disProg.subprogram_id = 0
            this.disProg.cluster_id = 0
            this.subprograms = []
            this.clusters = []

            var progId = this.disProg.program_id
            if(progId != 0){
                var program = this.programs.find(elem => elem.id == progId)
                this.subprograms = program.subprograms
            }
        },
        subpChange(){
            this.disProg.cluster_id = 0
            this.clusters = []
            var subpId = this.disProg.subprogram_id
            if(subpId != 0){
                var subprogram = this.subprograms.find(elem => elem.id == subpId)
                this.clusters = subprogram.clusters
            }
        },
        divChange(){
            this.disDiv.unit_id = 0
            this.disDiv.subunit_id = 0
            this.units = []
            this.subunits = []

            var divId = this.disDiv.division_id
            if(divId != 0){
                var division = this.divisions.find(elem => elem.id == divId)
                this.units = division.units
            }
        },
        unitChange(){
            this.disDiv.subunit_id = 0
            this.subunits = []
            var unitId = this.disDiv.unit_id
            if(unitId != 0){
                var unit = this.units.find(elem => elem.id == unitId)
                this.subunits = unit.subunits
            }
        },
        syncRecords(status = null){
            this.syncing = true
            var type = this.displaytype

            var options = (type == 'Program') ? this.disProg : this.disDiv
            options.type = type
            options.workshopId = this.$route.params.workshopId
            options.status     = status ? status : this.displaystatus
            this.fetchAnnexEs(options).then(res => {
                this.syncing = false
                this.displaystatus = options.status
                this.displaysyncstatus = options.status
            })
            
        },

        // Form
        submitForm(){
            if(this.formValidated()){
                this.saveAnnexE(this.form).then(res => {
                    var icon = res.errors ? 'error' : 'success'
                    this.toastMsg(icon, res.message)
                    if(!res.errors){
                        console.log(res.status)
                        this.syncRecords(res.status)
                        this.$refs.Close.click()
                        this.detailshow = false
                        this.formshow = false
                    }
                })
            }
        },
        formValidated(){
            var form = this.form
            var withSubtotal = this.withSubtotal(form.indicators)
            if(this.form.formtype == 'indicator'){
                for(let i = 0; i < form.indicators.length; i++){
                    var indicator = form.indicators[i]
                    if(indicator.description == ''){ this.toastMsg('warning', 'Performance Indicator Description Required'); return false }
                    if(withSubtotal && indicator.description.includes('Sub-Total')){ this.toastMsg('warning', 'Sub-Total already exists'); return false }
                }

                for(let i = 0; i < form.subs.length; i++){
                    withSubtotal = this.withSubtotal(form.subs[i].indicators)
                    for(let j = 0; j < form.subs[i].indicators.length; j++){
                        var indicator = form.subs[i].indicators[j]
                        if(indicator.description == ''){ this.toastMsg('warning', 'Performance Indicator Description Required'); return false }
                        if(withSubtotal && indicator.description.includes('Sub-Total')){ this.toastMsg('warning', 'Sub-Total already exists'); return false }
                    }
                }
            }
            return true
        },
        editForm(item, type){
            this.breakdownform = (item.project.program_id != 1)
            this.computeform = false
            this.totalSelectedId = 0
            this.form.id = item.id
            this.form.program_id = item.project.program_id
            this.form.project_id = item.project_id
            this.form.project_title = item.project.title
            this.form.indicators = []

            for(let i = 0; i < item.indicators.length; i++){
                var indicator = item.indicators[i]
                var tempIndicator = {
                    id: indicator.id,
                    description:      indicator.description,
                    actual:           (indicator.details === null) ? 0 : indicator.details.actual,
                    estimate:         (indicator.details === null) ? 0 : indicator.details.estimate,
                    physical_targets: (indicator.details === null) ? 0 : indicator.details.physical_targets,
                    first:            (indicator.details === null) ? 0 : indicator.details.first,
                    second:           (indicator.details === null) ? 0 : indicator.details.second,
                    third:            (indicator.details === null) ? 0 : indicator.details.third,
                    fourth:           (indicator.details === null) ? 0 : indicator.details.fourth,
                    common:           indicator.common,
                    breakdowns: [
                        {quarter: 1, fid: '', first: 0, sid: '', second: 0, tid: '', third: 0},
                        {quarter: 2, fid: '', first: 0, sid: '', second: 0, tid: '', third: 0},
                        {quarter: 3, fid: '', first: 0, sid: '', second: 0, tid: '', third: 0},
                        {quarter: 4, fid: '', first: 0, sid: '', second: 0, tid: '', third: 0},
                    ]
                }
                for(let j = 0; j < indicator.breakdowns.length; j++){
                    var breakdown = indicator.breakdowns[j]
                    var breakdownForm = tempIndicator.breakdowns.find(elem => elem.quarter == breakdown.quarter)
                    var idNum    = breakdown.month == 1 ? 'fid' : breakdown.month == 2 ? 'sid' : 'tid'
                    var monthNum = breakdown.month == 1 ? 'first' : breakdown.month == 2 ? 'second' : 'third'
                    breakdownForm[idNum]    = breakdown.id
                    breakdownForm[monthNum] = breakdown.number
                }

                this.form.indicators.push(tempIndicator)
            }

            this.form.subs = []
            for(let i = 0; i < item.subs.length; i++){
                var sub = item.subs[i]
                var tempSub = {
                    id: sub.id,
                    subproject_id: sub.subproject_id,
                    title: (sub.subproject_id) ? sub.subproject.title : (sub.temp_title == 'ms' ? 'MS' : sub.temp_title == 'phd' ? 'PhD' : sub.temp_title),
                    indicators: []
                }
                for(let j = 0; j < sub.indicators.length; j++){
                    var indicator = sub.indicators[j]
                    var tempIndicator = {
                        id: indicator.id,
                        description:      indicator.description,
                        actual:           (indicator.details === null) ? 0 : indicator.details.actual,
                        estimate:         (indicator.details === null) ? 0 : indicator.details.estimate,
                        physical_targets: (indicator.details === null) ? 0 : indicator.details.physical_targets,
                        first:            (indicator.details === null) ? 0 : indicator.details.first,
                        second:           (indicator.details === null) ? 0 : indicator.details.second,
                        third:            (indicator.details === null) ? 0 : indicator.details.third,
                        fourth:           (indicator.details === null) ? 0 : indicator.details.fourth,
                        common:           indicator.common,
                        breakdowns: [
                            {quarter: 1, first: 0, second: 0, third: 0},
                            {quarter: 2, first: 0, second: 0, third: 0},
                            {quarter: 3, first: 0, second: 0, third: 0},
                            {quarter: 4, first: 0, second: 0, third: 0},
                        ]
                    }

                    tempSub.indicators.push(tempIndicator)
                }
                this.form.subs.push(tempSub)
            }

            this.detailshow = (type == 'details')
            this.indicatorshow = (type == 'indicator')
            this.form.formtype = type
        },
        addIndicator(id = null){
            var item = id ? this.form.subs.find(elem => elem.id == id) : this.form
            var tempIndicator = {
                id: '',
                description: '',
                common: false
            }
            item.indicators.push(tempIndicator)
        },
        removeIndicator(indicator, id = null){
            var item = id ? this.form.subs.find(elem => elem.id == id) : this.form
            item.indicators.remove(indicator)
        },
        showComputeForm(){
            this.indicatorshow = false
            this.computeform = true
        },
        showIndicatorBreakdown(key){
            this.indicatorshow = false
            this.breakdownform = true
            this.breakdownkey = key
        },
        totalIndicatorBreakdown(key, col){
            var result = 0
            if(col != 'physical_targets'){
                var quarter = col == 'first' ? 1 : col == 'second' ? 2 : col == 'third' ? 3 : 4
                var breakdown = this.form.indicators[key].breakdowns.find(elem => elem.quarter == quarter)
                for(let j = 0; j < this.breakdownmonths.length; j++){
                    var col = this.breakdownmonths[j]
                    result = result + this.strToFloat(breakdown[col])
                }
            }
            else{
                for(let i = 0; i < this.form.indicators[key].breakdowns.length; i++){
                    var breakdown = this.form.indicators[key].breakdowns[i]
                    for(let j = 0; j < this.breakdownmonths.length; j++){
                        var col = this.breakdownmonths[j]
                        result = result + this.strToFloat(breakdown[col])
                    }
                }
            }
            return result
        },
        totalIndicator(col){
            var result = 0
            for(let i = 0; i < this.form.indicators.length; i++){
                var indicator = this.form.indicators[i]
                if(indicator.id != this.totalSelectedId && !indicator.description.includes('Sub-Total')){
                    result = result + this.strToFloat(indicator[col])
                }
            }
            for(let i = 0; i < this.form.subs.length; i++){
                for(let j = 0; j < this.form.subs[i].indicators.length; j++){
                    var indicator = this.form.subs[i].indicators[j]
                    if(!indicator.description.includes('Sub-Total')){
                        result = result + this.strToFloat(indicator[col])
                    }
                }
            }
            return result
        },
        subtotalIndicator(col, id = null){
            var result = 0
            var item = id ? this.form.subs.find(elem => elem.id == id) : this.form
            for(let i = 0; i < item.indicators.length; i++){
                var indicator = item.indicators[i]
                if(!indicator.description.includes('Sub-Total')){
                    result = result + this.strToFloat(indicator[col])
                }
            }
            return result
        },
        withSubtotal(indicators){
            var state = false
            for(let i = 0; i < indicators.length; i++){
                if(!state){
                    state = (indicators[i].description.includes('Sub-Total'))
                }
            }
            return state
        },
        appendSubtotal(id = null){
            var item = id ? this.form.subs.find(elem => elem.id == id) : this.form
            var subtotalIndicator = {
                id: id + Math.random(),
                description: 'Sub-Total',
                actual: 0,
                estimate: 0,
                physical_targets: 0,
                first: 0,
                second: 0,
                third: 0,
                fourth: 0
            }
            item.indicators.push(subtotalIndicator)
        },
        removeSubtotal(id = null){
            var item = id ? this.form.subs.find(elem => elem.id == id) : this.form
            var indicator = item.indicators.find(elem => elem.description == 'Sub-Total')
            item.indicators.remove(indicator)
        },
        formatQtr(qtr){
            return (qtr == 1) ? '1st' : (qtr == 2) ? '2nd' : (qtr == 3) ? '3rd' : '4th'
        },
        formatMonth(qtr, month){
            switch(qtr){
                case 1:
                    return (month == 'first') ? 'Jan' : (month == 'second') ? 'Feb' : 'Mar'
                case 2:
                    return (month == 'first') ? 'Apr' : (month == 'second') ? 'May' : 'Jun'
                case 3: 
                    return (month == 'first') ? 'Jul' : (month == 'second') ? 'Aug' : 'Sep'
                default:
                    return (month == 'first') ? 'Oct' : (month == 'second') ? 'Nov' : 'Dec'
            }
        },
        formatNumber(num){
            return (Math.round(num * 100) / 100).toLocaleString(undefined, {minimumFractionDigits: 0, maximumFractionDigits: 0 })
        },
        strToFloat(num){
            let strNum = num.toString().replace(/\,/g,'')
            return parseFloat(strNum)
        },
        // Toast
        toastMsg(icon, msg){
            toast.fire({
                icon: icon,
                title: msg
            })
        },
    },
    computed: {
        ...mapGetters('workshop', ['getWorkshop']),
        workshop(){ return this.getWorkshop },
        ...mapGetters('program', ['getPrograms']),
        programs(){ return this.getPrograms },
        ...mapGetters('division', ['getDivisions']),
        divisions(){ return this.getDivisions },
        ...mapGetters('annexe', ['getAnnexEs']),
        annexes(){ return this.getAnnexEs }
    },
    created(){
        this.fetchWorkshop(this.$route.params.workshopId).then(res => {
            // this.fetchAnnexEs(this.$route.params.workshopId).then(res => {
                this.loading = false
            // })
        })
        if(this.programs.length == 0){
            this.fetchPrograms()
        }
        if(this.divisions.length == 0){
            this.fetchDivisions()
        }
    }
}
</script>
<style scoped>
.card-overlay{
    background: rgba(0,0,0,0.5);
    width: 100%;
    height: 100%;
    position: absolute;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    top: 0;
    left: 0;
}
.indicator-input{
    border-radius: 0!important;
    text-align: right!important;
    border: 0!important;
    box-shadow: none!important;
    height: 100%;
}
.indicator-input:active, .indicator-input:focus{
    background: lightcyan;
}
</style>