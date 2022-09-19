<template>
    <div class="px-3 py-4" style="calc(100vh - 45px)">
        <div class="border-bottom mb-2">
            <button class="btn btn-sm btn-light float-start" @click="this.$router.push('/budget-executive-documents')"><i class="fas fa-arrow-left"></i> Back</button>
            <h4 class="text-center">Annex E</h4>
            <small>Planning Workshop <span v-if="!loading">{{workshop.date}}</span><span v-else>Loading date <i class="fas fa-spinner fa-spin"></i></span></small>
        </div>
        <template v-if="!loading">
            <div class="d-flex justify-content-between mb-2">
                <div>
                    <button v-if="!editmode" class="btn btn-sm shadow-none min-100 me-2" :class="printmode ? 'btn-success' : 'btn-secondary'" @click="printmode = !printmode">{{!printmode? 'Print' : 'Display'}} View</button>
                    <button v-if="!editmode && printmode && annexes.length > 0" v-print="'#printMe'" class="btn btn-sm btn-outline-secondary"><i class="far fa-print"></i> Print</button>
                </div>
                <button v-if="annexes.length > 0 && !detailshow" class="btn shadow-none btn-sm" :class="!editmode ? 'btn-success' : 'btn-primary'" @click="editmode = !editmode">{{editmode ? 'View' : 'Edit'}} Mode</button>
            </div>
            <div class="row flex-row-reverse" v-if="!formshow && !detailshow">
                <div class="col-sm-3" v-if="filtershow">
                    <div class="card border-0 shadow mb-3">
                        <div class="card-body">
                            <div class="d-flex justify-content-end mb-2">
                                <button class="btn btn-sm btn-outline-secondary" @click="filtershow = false"><i class="fas fa-arrow-right"></i></button>
                            </div>
                            <div class="form-floating mb-3">
                                <select class="form-control" id="Status" v-model="displaystatus">
                                    <option value="New">New</option>
                                    <option value="Draft">Draft</option>
                                    <option value="For Review">For Review</option>
                                    <option value="For Approval">For Approval</option>
                                    <option value="Approved">Approved</option>
                                    <option value="Submitted">Submitted</option>
                                </select>
                                <label for="Status">PI Status</label>
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
                <div class="position-relative" :class="'col-sm-'+(filtershow ? '9': '12')">
                    <button class="btn btn-sm btn-outline-secondary float-end ms-1" v-if="!filtershow" @click="filtershow = true"><i class="fas fa-arrow-left"></i></button>
                    <!-- Display View -->
                    <template v-if="!editmode">
                        <div class="card border-0 shadow overflow-auto" :style="!printmode ? 'height: calc(100vh - 210px)' : ''">
                            <div class="card-body">
                                <div class="card-overlay" v-if="syncing"><h1><i class="fas fa-spinner fa-spin fa-5x"></i></h1> </div>
                                <Display v-if="annexes.length > 0" :printmode="printmode" :syncing="syncing" :displaysyncstatus="displaysyncstatus" id="printMe"/>
                                <EmptyTable v-else />
                            </div>
                        </div>
                    </template>
                    <!-- Edit View -->
                    <div v-else>
                        <div class="text-center">
                            <button class="btn mb-2 shadow-none bg-gradient me-1" :class="tab == 'performance' ? 'btn-primary' : 'btn-outline-primary'" @click="tab = 'performance'">Performance</button>
                            <button class="btn mb-2 shadow-none bg-gradient" :class="tab != 'performance' ? 'btn-primary' : 'btn-outline-primary'" @click="tab = 'other'">Outcome & Output</button>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2" v-if="tab == 'performance'">
                            <span class="fw-bold  px-4 py-1 rounded">
                                <i class="fas" :class="setStatusIcon(displaysyncstatus)"></i>  
                                {{(displaysyncstatus == 'New') ? 'Newly Added Projects!' : (displaysyncstatus == 'Draft' ? 'Drafts' : displaysyncstatus)}}
                            </span>
                        </div>
                        <div class="table-responsive" style="height: 64vh">
                            <table class="table table-sm table-bordered table-hover">
                                <thead class="text-center">
                                    <tr>
                                        <th style="width: 40%" v-if="tab == 'performance'"><span class="text-nowrap">Program/Project</span></th>
                                        <th style="width: 40%"><span class="text-nowrap">{{tab == 'performance' ? 'Performance' : 'Outcome & Output'}} Indicators (PIs)</span></th>
                                        <th style="width: 20%">Actions</th>
                                    </tr>
                                </thead>
                                <tbody >
                                    <template v-if="!syncing">
                                        <template v-for="program in annexes" :key="'program_'+program.id">
                                            <tr v-if="(program.subpwithitems || program.items.length > 0) && tab == 'performance'"><th class="bg-success text-white" colspan="3">{{program.title}}</th></tr>
                                            <tr v-if="(program.subpwithci || program.commonindicators.length > 0) && tab != 'performance'"><th class="bg-success text-white" colspan="3">{{program.title}}</th></tr>
                                            <template v-if="tab == 'performance'">
                                                <template v-for="item in program.items" :key="'cluster-item_'+item.id">
                                                    <tr>
                                                        <td><div class="ms-3"><i class="fas me-2" :class="setStatusIcon(item.status)"></i>{{item.project.title}}</div></td>
                                                        <td><li v-for="indicator in item.indicators" :key="'indicator_'+indicator.id">{{indicator.description}}</li></td>
                                                    </tr>
                                                    <tr v-for="sub in item.subs" :key="'sub_'+sub.id">
                                                        <td><div class="ms-4">{{setSubTitle(sub)}}</div></td>
                                                        <td><li v-for="indicator in sub.indicators" :key="'indicator_'+indicator.id">{{indicator.description}}</li></td>
                                                        <td class="text-center" :rowspan="item.subs.length + 1">
                                                            <template v-if="checkUserDivision(item.project)">
                                                                <button class="min-100 shadow-none btn btn-sm btn-outline-secondary me-1 mb-1" @click="editForm(item, 'indicator')"  v-if="!isForReview(item.status)" data-bs-toggle="modal" data-bs-target="#form"><i class="far fa-pencil-alt"></i> Indicators</button><br>
                                                                <button class="min-100 shadow-none btn btn-sm btn-outline-secondary me-1 mb-1" @click="editForm(item, 'details')"><i class="far" :class="!isForReview(item.status) ? 'fa-pencil-alt' : 'fa-search'"></i> {{!isForReview(item.status) ? 'Details' : 'Review'}}</button><br>
                                                                <button class="min-100 shadow-none btn btn-sm btn-outline-secondary me-1 mb-1" v-if="item.histories.length > 0" @click="setHistory(item)" data-bs-toggle="modal" data-bs-target="#history"><i class="far fa-clipboard-list"></i> Logs</button>
                                                                <!-- <button class="min-100 shadow-none btn btn-sm btn-danger me-1 mb-1" v-if="!isForReview(item.status)"><i class="far fa-trash-alt"></i> Remove</button> -->
                                                            </template>
                                                        </td>
                                                    </tr>
                                                </template>
                                            </template>
                                            <template v-else>
                                                <template v-for="indicator in program.commonindicators" :key="'program_commonindicator_'+indicator.id">
                                                    <tr>
                                                        <td>{{indicator.description}} </td>
                                                        <td :rowspan="indicator.subindicators.length+1" class="text-center">
                                                            <button class="btn btn-sm btn-outline-secondary min-100" v-if="indicator.tags.length == 0" @click="editFormTwo(indicator)" data-bs-target="#form2" data-bs-toggle="modal"><i class="far fa-pencil-alt"></i> Edit</button>
                                                        </td>
                                                    </tr>
                                                    <tr v-for="sub in indicator.subindicators" :key="'program_commonindicator_sub_'+sub.id">
                                                        <td><div class="ms-2">{{sub.description}}</div></td>
                                                    </tr>
                                                </template>
                                            </template>
                                            <template v-for="subprogram in program.subprograms" :key="'subprogram_'+subprogram.id">
                                                <tr v-if="(subprogram.items.length > 0 || subprogram.cluswithitems) && tab == 'performance'"><th colspan="3">{{program.id == 1 ? subprogram.title_short : subprogram.title}}</th></tr>
                                                <tr v-if="(subprogram.commonindicators.length > 0 || subprogram.cluswithci) && tab != 'performance'"><th colspan="3">{{program.id == 1 ? subprogram.title_short : subprogram.title}}</th></tr>
                                                <template v-if="tab == 'performance'">
                                                    <template v-for="item in subprogram.items" :key="'cluster-item_'+item.id">
                                                        <tr>
                                                            <td><div class="ms-3"><i class="fas me-2" :class="setStatusIcon(item.status)"></i>{{item.project.title}}</div></td>
                                                            <td><li v-for="indicator in item.indicators" :key="'indicator_'+indicator.id">{{indicator.description}}</li></td>
                                                            <td class="text-center" :rowspan="item.subs.length + 1">
                                                                <template v-if="checkUserDivision(item.project)">
                                                                    <button class="min-100 shadow-none btn btn-sm btn-outline-secondary me-1 mb-1" @click="editForm(item, 'indicator')"  v-if="!isForReview(item.status)" data-bs-toggle="modal" data-bs-target="#form"><i class="far fa-pencil-alt"></i> Indicators</button><br>
                                                                    <button class="min-100 shadow-none btn btn-sm btn-outline-secondary me-1 mb-1" @click="editForm(item, 'details')"><i class="far" :class="!isForReview(item.status) ? 'fa-pencil-alt' : 'fa-search'"></i> {{!isForReview(item.status) ? 'Details' : 'Review'}}</button><br>
                                                                    <button class="min-100 shadow-none btn btn-sm btn-outline-secondary me-1 mb-1" v-if="item.histories.length > 0" @click="setHistory(item)" data-bs-toggle="modal" data-bs-target="#history"><i class="far fa-clipboard-list"></i> Logs</button>
                                                                    <!-- <button class="min-100 shadow-none btn btn-sm btn-danger me-1 mb-1" v-if="!isForReview(item.status)"><i class="far fa-trash-alt"></i> Remove</button> -->
                                                                </template>
                                                            </td>
                                                        </tr>
                                                        <tr v-for="sub in item.subs" :key="'sub_'+sub.id">
                                                            <td><div class="ms-4">{{setSubTitle(sub)}}</div></td>
                                                            <td><li v-for="indicator in sub.indicators" :key="'indicator_'+indicator.id">{{indicator.description}}</li></td>
                                                        </tr>
                                                    </template>
                                                </template>
                                                <template v-else>
                                                    <template v-for="indicator in subprogram.commonindicators" :key="'program_commonindicator_'+indicator.id">
                                                        <tr>
                                                            <td>{{indicator.description}} </td>
                                                            <td :rowspan="indicator.subindicators.length+1"></td>
                                                        </tr>
                                                        <tr v-for="sub in indicator.subindicators" :key="'program_commonindicator_sub_'+sub.id">
                                                            <td><div class="ms-2">{{sub.description}}</div></td>
                                                        </tr>
                                                    </template>
                                                </template>
                                                <template v-for="cluster in subprogram.clusters" :key="'cluster_'+cluster.id">
                                                    <tr v-if="cluster.items.length > 0 && tab == 'performance'"><th colspan="3"><div class="ms-2">{{cluster.title}}</div></th></tr>
                                                    <tr v-if="cluster.commonindicators.length > 0 && tab != 'performace'"><th colspan="3"><div class="ms-2">{{cluster.title}}</div></th></tr>
                                                    <template v-if="tab == 'performance'">
                                                        <template v-for="item in cluster.items" :key="'cluster-item_'+item.id">
                                                            <tr>
                                                                <td><div class="ms-3"><i class="fas me-2" :class="setStatusIcon(item.status)"></i>{{item.project.title}}</div></td>
                                                                <td><li v-for="indicator in item.indicators" :key="'indicator_'+indicator.id">{{indicator.description}}</li></td>
                                                                <td class="text-center" :rowspan="item.subs.length+1">
                                                                    <template v-if="checkUserDivision(item.project)">
                                                                        <button class="min-100 shadow-none btn btn-sm btn-outline-secondary me-1 mb-1" @click="editForm(item, 'indicator')"  v-if="!isForReview(item.status)" data-bs-toggle="modal" data-bs-target="#form"><i class="far fa-pencil-alt"></i> Indicators</button><br>
                                                                        <button class="min-100 shadow-none btn btn-sm btn-outline-secondary me-1 mb-1" @click="editForm(item, 'details')"><i class="far" :class="!isForReview(item.status) ? 'fa-pencil-alt' : 'fa-search'"></i> {{!isForReview(item.status) ? 'Details' : 'Review'}}</button><br>
                                                                        <button class="min-100 shadow-none btn btn-sm btn-outline-secondary me-1 mb-1" v-if="item.histories.length > 0" @click="setHistory(item)" data-bs-toggle="modal" data-bs-target="#history"><i class="far fa-clipboard-list"></i> Logs</button>
                                                                        <!-- <button class="min-100 shadow-none btn btn-sm btn-danger me-1 mb-1" v-if="!isForReview(item.status)"><i class="far fa-trash-alt"></i> Remove</button> -->
                                                                    </template>
                                                                </td>
                                                            </tr>
                                                            <tr v-for="sub in item.subs" :key="'sub_'+sub.id">
                                                                <td><div class="ms-4">{{setSubTitle(sub)}}</div></td>
                                                                <td><li v-for="indicator in sub.indicators" :key="'indicator_'+indicator.id">{{indicator.description}}</li></td>
                                                            </tr>
                                                        </template>
                                                    </template>
                                                    <template v-else>
                                                        <template v-for="indicator in subprogram.commonindicators" :key="'program_commonindicator_'+indicator.id">
                                                            <tr>
                                                                <td>{{indicator.description}} </td>
                                                                <td :rowspan="indicator.subindicators.length+1"></td>
                                                            </tr>
                                                            <tr v-for="sub in indicator.subindicators" :key="'program_commonindicator_sub_'+sub.id">
                                                                <td><div class="ms-2">{{sub.description}}</div></td>
                                                            </tr>
                                                        </template>
                                                    </template>
                                                </template>
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
            <div v-if="detailshow && !detailsyncing">
                <template v-if="!detailsyncing">
                    <div class="d-flex justify-content-between mb-3" v-if="!saving">
                        <div>
                            <button class="btn btn-sm btn-danger me-1" @click="detailshow = false"><i class="fas fa-times"></i> Cancel</button>
                            <button :class="saving ? 'disabled' : ''" class="btn btn-sm btn-outline-secondary" v-if="histories.length > 0" data-bs-toggle="modal" data-bs-target="#history"><i class="far fa-clipboard-list"></i> Logs</button>
                        </div>
                        <div v-if="!isForReview(form.status)">
                            <button class="btn btn-sm btn-outline-secondary me-1" @click="showComputeForm()" v-if="form.program_id == 1 && checkUserTitle(form.status) && isUserProjectLeader(form.leaderId)" data-bs-toggle="modal" data-bs-target="#detailform"><i class="far fa-cogs"></i></button>
                            <button class="btn btn-sm btn-secondary me-1"         @click="submitForm('Draft')"      v-if="checkUserTitle(form.status) && isUserProjectLeader(form.leaderId)"><i class="fas fa-edit"></i> Draft</button>
                            <button class="btn btn-sm btn-success"                @click="submitForm('For Review')" v-if="checkUserTitle(form.status) && isUserProjectLeader(form.leaderId)"><i class="fas fa-search"></i> {{authuser.active_profile.title.name == 'Unit Head' ? 'For Approval' : 'For Review'}}</button>
                        </div>
                        <div v-if="isForReview(form.status) && form.status != 'Submitted'">
                            <button class="btn btn-sm btn-secondary min-100 me-1" v-if="checkUserTitle(form.status)" @click="indicatorshow = false" data-bs-target="#form" data-bs-toggle="modal"><i class="fas fa-times"></i> Reject</button>
                            <button class="btn btn-sm btn-success min-100 me-1"   v-if="checkUserTitle(form.status)" @click="submitForm('approve')"><i class="fas fa-check"></i> {{form.status == 'Approved' ? 'Submit to Planning Unit' : 'Approve'}}</button>
                        </div>
                    </div>
                    <div class="table-responsive" style="height: calc(100vh - 218px);" v-dragscroll>
                        <table class="table table-sm table-bordered">
                            <TableHead :syncing="true" :printmode="true" />
                            <tbody>
                                <tr><td :rowspan="form.indicators.length + 1">{{form.project_title}}</td></tr>
                                <tr v-for="indicator, key in form.indicators" :key="'indicator_'+key">
                                    <td style="height: 1px" class="p-0 align-middle" v-for="col in indcols" :key="col">
                                        <template v-if="form.status == 'New' || form.status == 'Draft'">
                                            <!-- Program 1: S&T Scholarship Program -->
                                            <template v-if="form.program_id == 1">
                                                <p :class="indicator.id == totalSelectedId ? 'fw-bold' : ''" class="px-2 py-1 m-0" v-if="col == 'description'">{{indicator[col]}}</p>
                                                <template v-else>
                                                    <template v-if="indicator.description != 'Sub-Total'">
                                                        <p v-if="indicator.id == totalSelectedId" class="px-2 py-1 m-0 text-end fw-bold">{{indicator[col] = formatNumber(totalIndicator(col))}}</p>
                                                        <input v-else type="text" v-model="indicator[col]" v-money="money" class="form-control indicator-input">
                                                    </template>
                                                    <template v-else>
                                                        <p class="px-2 py-1 m-0 text-end fw-bold">{{indicator[col] = formatNumber(subtotalIndicator(col))}}</p>
                                                    </template>
                                                </template>
                                            </template>
                                            <!-- Program 2: S&T Educational Development Program -->
                                            <template v-else>
                                                <p     class="px-2 py-1 m-0"                v-if="col == 'description'">{{indicator[col]}}</p>
                                                <input class="form-control indicator-input" v-else-if="col == 'actual' || col == 'estimate'" type="text" v-model="indicator[col]" v-money="money">
                                                <p     class="px-2 py-1 m-0 text-end"       v-else @click="showIndicatorBreakdown(key)" data-bs-toggle="modal" data-bs-target="#detailform" style="cursor: pointer;">{{indicator[col] = formatNumber(totalIndicatorBreakdown(key, col))}}</p>
                                            </template>
                                        </template>
                                        <p v-else class="px-2 py-1 m-0" :class="col != 'description' ? 'text-end' : ''">{{indicator[col]}}</p>
                                    </td>
                                </tr>
                                <template v-for="sub, skey in form.subs" :key="'sub_'+skey">
                                    <tr><td :rowspan="sub.indicators.length+1">{{sub.title}}</td></tr>      
                                    <tr v-for="indicator, key in sub.indicators" :key="'indicator_'+key">
                                        <td style="height: 1px" class="p-0 align-middle" v-for="col in indcols" :key="col">
                                            <template v-if="form.status == 'New' || form.status == 'Draft'">
                                                <!-- Program 1: S&T Scholarship Program -->
                                                <template v-if="form.program_id == 1">
                                                    <p class="px-2 py-1 m-0" v-if="col == 'description'">{{indicator[col]}}</p>
                                                    <template v-else>
                                                        <template v-if="indicator.description != 'Sub-Total'">
                                                            <p v-if="indicator.id == totalSelectedId" class="px-2 py-1 m-0 text-end fw-bold">{{indicator[col] = formatNumber(totalIndicator(col))}}</p>
                                                            <input v-else type="text" v-model="indicator[col]" v-money="money" class="form-control indicator-input">
                                                        </template>
                                                        <template v-else>
                                                            <p class="px-2 py-1 m-0 text-end fw-bold">{{indicator[col] = formatNumber(subtotalIndicator(col, sub.id))}}</p>
                                                        </template>
                                                    </template>
                                                </template>
                                                <!-- Program 2: S&T Educational Development Program -->
                                                <template v-else>
                                                    <p     class="px-2 py-1 m-0"                v-if="col == 'description'">{{indicator[col]}}</p>
                                                    <input class="form-control indicator-input" v-else-if="col == 'actual' || col == 'estimate'" type="text" v-model="indicator[col]" v-money="money">
                                                    <p     class="px-2 py-1 m-0 text-end"       v-else @click="showIndicatorBreakdown(key, skey)" data-bs-toggle="modal" data-bs-target="#detailform" style="cursor: pointer;">{{indicator[col] = formatNumber(totalIndicatorBreakdown(key, col, skey))}}</p>
                                                </template>
                                            </template>
                                            <p v-else class="px-2 py-1 m-0" :class="col != 'description' ? 'text-end' : ''">{{indicator[col]}}</p>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </template>
                <h1 class="text-center p-5" v-else> <i class="fas fa-spinner fa-spin"></i> Loading Resources </h1>
            </div>
        </template>
        <h1 v-else class="text-center p-5"><i class="fas fa-spinner fa-spin fa-5x"></i></h1>
    </div>
    <Logs :histories="histories" :title="historyfor"/>
    <!-- Modal -->
    <div class="modal fade" id="detailform" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered" :class="!breakdownform ? 'modal-lg' : ''">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" v-if="breakdownform && form.indicators.length > 0">{{form.indicators[breakdownkey].description}}</h5>
                <h5 class="modal-title" v-if="computeform"> <span>{{form.project_title}} Performance Indicators</span></h5>
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
                    <tbody v-if="form.indicators.length > 0">
                        <!-- <tr v-for="breakdown in form.indicators[breakdownkey].breakdowns" :key="'quarter_'+breakdown.quarter"> -->
                        <tr v-for="breakdown in breakdowns" :key="'quarter_'+breakdown.quarter">
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
            <div class="modal-footer">
                <button @click="submitForm()" type="button" v-if="indicatorshow" class="btn btn-primary rounded-pill" style="min-width: 100px">Save</button>
                <button type="button" v-else :class="computeform ? 'btn-primary' : 'btn-secondary'" class="btn rounded-pill" data-bs-dismiss="modal" style="min-width: 100px">{{ computeform ? 'Save' : 'Done'}}</button>
            </div>
            </div>
        </div>
    </div>
    <!-- Modal -->
    <div class="modal fade" id="form" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content" v-if="indicatorshow">
                <div class="modal-header">
                    <h5 class="modal-title"> <span>Performance Indicators</span></h5>
                    <button type="button" ref="closebtn" class="btn-close"  data-bs-dismiss="modal" aria-label="close"></button>
                </div>
                <div class="modal-body p-0 overflow-auto" style="height: 50vh;">
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
                    <button @click="submitForm('same')" type="button" class="btn btn-primary rounded-pill" style="min-width: 100px">Save</button>
                </div>
            </div>
            <div class="modal-content" v-else>
                <div class="modal-body">
                    <div class="d-flex justify-content-end mb-3">
                        <button type="button" ref="closebtn" class="btn-close" style="display: none" data-bs-dismiss="modal" aria-label="close"></button>
                    </div>
                    <div class="form-floating mb-3">
                        <textarea class="form-control" placeholder="Leave a comment here" v-model="form.comment" id="Comment" style="height: 200px"></textarea>
                        <label for="Comment">Please add comment <span class="text-danger">*</span></label>
                    </div>
                    <div class="d-flex justify-content-end">
                        <button class="btn btn-sm btn-success rounded-pill min-100" @click="submitForm('reject')">Submit</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal -->
    <div class="modal fade" id="form2" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"> <span>{{form2.type}} Indicator</span></h5>
                    <button type="button" ref="Close" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body overflow-auto" style="max-height: 80vh">
                    <div class="row">
                        <div class="col-sm-4"><h6>{{form2.description}}</h6></div>
                        <div class="col-sm-8">
                            <div class="form-group row" v-for="row in 2" :key="'row-'+row">
                                <div :class="'col-sm'+(num < 3 ? '-3' : '')" v-for="num in (row == 1 ? 3 : 4)" :key="num">
                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control form-control-sm text-end" v-model="form2[setFormModel(row, num)]" v-money="money" :id="'1_'+num" :placeholder="num">
                                        <label :for="'1_'+num">{{setFormLabel(row, num)}}</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div v-for="sub in form2.subs" :key="'subform2_'+sub.id">
                        <hr>
                        <div class="row">
                            <div class="col-sm-4"><h6>{{sub.description}}</h6></div>
                            <div class="col-sm-8">
                                <div class="form-group row" v-for="row in 2" :key="'row-'+row">
                                    <div :class="'col-sm'+(num < 3 ? '-3' : '')" v-for="num in (row == 1 ? 3 : 4)" :key="num">
                                        <div class="form-floating mb-3">
                                            <input type="text" class="form-control form-control-sm text-end" v-model="sub[setFormModel(row, num)]" v-money="money" :id="'1_'+num" :placeholder="num">
                                            <label :for="'1_'+num">{{setFormLabel(row, num)}}</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary rounded-pill min-100" @click="submitFormTwo()">Save</button>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import EmptyTable from './EmptyTable.vue'
import Display from './Display.vue'
import TableHead from './TableHead.vue'
import Logs from './Logs.vue'
import { mapActions, mapGetters } from 'vuex'
import { dragscroll } from 'vue-dragscroll'
export default {
    name: 'AnnexE',
    directives: {
        dragscroll: dragscroll,
    },
    components: { EmptyTable, Display, TableHead, Logs },
    data(){
        return {
            filtershow: true,
            tab: 'performance',
            printmode: false,
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
            
            // performance form
            formshow: false,
            detailshow: false,
            detailsyncing: false,
            form: {
                id: '',
                program_id: 0,
                project_id: '',
                project_title: '',
                indicators: [],
                subs: [],
                formtype: '',
                status: 'Draft',
                comment: '',
                leaderId: ''
            },
            // outcome & output form
            form2: {
                id: '',
                description: '',
                tag: null,
                type: '',
                actual: '',
                estimate: '',
                physical_targets: '',
                first: '', second: '', third: '', fourth: '',
                subs: []
            },
            // indicator columns
            indcols: ['description', 'actual', 'estimate', 'physical_targets', 'first', 'second', 'third', 'fourth'],
            breakdowns: [],
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
            indicatorshow: false,
            prevstatus: '',
            saving: false,
            // logs
            histories: [],
            historyfor: ''
        }
    },
    methods: {
        ...mapActions('workshop', ['fetchWorkshop']),
        ...mapActions('annexe', ['fetchAnnexEs', 'fetchAnnexE', 'saveAnnexE', 'updateAnnexEOther']),
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
        submitForm(status){
            this.saving = true
            var validate = true
            this.prevstatus = this.form.status
            var stat = this.form.status
            if(status != 'approve' || status != 'reject'){
                validate = this.formValidated()
                if(!validate){
                    status = this.prevstatus
                    this.saving = false
                }
            }
            if(status == 'approve'){
                status = (stat == 'For Review') ? 'For Approval' : (stat == 'For Approval') ? 'Approved' : 'Submitted'
            }
            if(status == 'reject'){
                if(this.form.comment == ''){
                    this.toastMsg('warning', 'Comment required')
                    return false
                }
                status = 'Draft'
            }
            if(status == 'For Review'){
                status = this.authuser.active_profile.title.name == 'Unit Head' ? 'For Approval' : 'For Review'
            }
            this.form.status = status
            
            if(validate){
                this.saveAnnexE(this.form).then(res => {
                    var icon = res.errors ? 'error' : 'success'
                    this.toastMsg(icon, res.message)
                    if(!res.errors){
                        this.syncRecords(res.status)
                        this.$refs.closebtn.click()
                        this.detailshow = false
                        this.formshow = false
                    }
                    this.saving = false
                })
            }
        },
        formValidated(){
            var form = this.form
            var withSubtotal = this.withSubtotal(form.indicators)
            // if(this.form.formtype == 'indicator'){
            var state = false
            for(let i = 0; i < form.indicators.length; i++){
                var indicator = form.indicators[i]
                if(this.form.formtype == 'indicator'){
                    if(indicator.description == ''){ this.toastMsg('warning', 'Performance Indicator Description Required'); return false }
                    if(withSubtotal && indicator.description.includes('Sub-Total')){ this.toastMsg('warning', 'Sub-Total already exists'); return false }
                }
                if(!state){ state = this.checkIndicator(indicator) }
            }

            for(let i = 0; i < form.subs.length; i++){
                withSubtotal = this.withSubtotal(form.subs[i].indicators)
                for(let j = 0; j < form.subs[i].indicators.length; j++){
                    var indicator = form.subs[i].indicators[j]
                    if(this.form.formtype == 'indicator'){
                        if(indicator.description == ''){ this.toastMsg('warning', 'Performance Indicator Description Required'); return false }
                        if(withSubtotal && indicator.description.includes('Sub-Total')){ this.toastMsg('warning', 'Sub-Total already exists'); return false }
                    }
                    if(!state){ state = this.checkIndicator(indicator) }
                }
            }

            
            if((form.status != 'New' || form.status != 'Draft') && form.formtype == 'details'){
                if(!state){ this.toastMsg('warning', 'Fill up indicator fields'); this.form.status = this.prevstatus; return false }
            }
            // }

            return true
        },
        checkIndicator(indicator){
            var state = false
            for(let i = 0; i < this.indcols.length; i++){
                var col = this.indcols[i]
                if(col != 'description'){
                    var intcol = this.strToFloat(indicator[col])
                    if(!state){
                        state = (intcol > 0)
                    }
                }
            }
            return state
        },
        async editForm(item, type){
            this.detailsyncing = true
            this.detailshow = (type == 'details')
            this.indicatorshow = (type == 'indicator')
            this.computeform = false
            this.totalSelectedId = 0
            this.breakdownkey = 0
            this.breakdownform = (item.project.program_id != 1)
            this.form.formtype = type
            this.form.id = item.id
            this.form.program_id = item.project.program_id
            this.form.project_id = item.project_id
            this.form.project_title = item.project.title
            this.form.status = item.status
            this.histories = item.histories
            this.historyfor = item.project.title
            this.form.leaderId = item.project.leader.profile_id
            this.form.comment = ''
            this.form.subs = []
            for(let i = 0; i < item.subs.length; i++){
                var sub = item.subs[i]
                var tempSub = {
                    id: sub.id,
                    subproject_id: sub.subproject_id,
                    title: (sub.subproject_id) ? sub.subproject.title : (sub.temp_title == 'ms' ? 'MS' : sub.temp_title == 'phd' ? 'PhD' : sub.temp_title),
                    indicators: []
                }

                await this.fillFormIndicators(sub, tempSub).then(res => {
                    this.form.subs.push(tempSub)
                })
            }
            
            this.form.indicators = []
            await this.fillFormIndicators(item).then(res => {
                this.detailsyncing = false
            })
        },
        async fillFormIndicators(item, tempSub = false){
            const promise = await new Promise((resolve, reject) => {
                var time = 0
                setTimeout(async() => {
                    var start = new Date().getTime();
                    for(let i = 0; i < item.indicators.length; i++){
                        var indicator = item.indicators[i]
                        var tempIndicator = {
                            id: indicator.id,
                            description:      indicator.description,
                            tag:              indicator.tags.length > 0 ? indicator.tags[0] : null  ,
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
                        await this.fillBreakdown(tempIndicator, indicator).then(res => {
                            if(!tempSub){
                                this.form.indicators.push(res) 
                            }
                            else{
                                tempSub.indicators.push(res)
                            }
                        })
                    }

                    var end = new Date().getTime();
                    time = end - start;
                    resolve('done')
                }, time)
            })
            return promise
        },
        async fillBreakdown(tempIndicator, indicator){
            const p = await new Promise((resolve, reject) => {
                var time = 0
                setTimeout(() => {
                    var start = new Date().getTime();
                    for(let j = 0; j < indicator.breakdowns.length; j++){
                        var breakdown = indicator.breakdowns[j]
                        var breakdownForm = tempIndicator.breakdowns.find(elem => elem.quarter == breakdown.quarter)
                        this.fillBreakdownDetail(breakdown, breakdownForm)
                    }

                    var end = new Date().getTime();
                    time = end - start;
                    resolve(tempIndicator)
                }, time)
            })
            return p
        },
        async fillBreakdownDetail(breakdown, breakdownForm){
            const p = await new Promise(async (resolve, reject) => {
                var time = 0
                setTimeout(()  => {
                    var start = new Date().getTime();

                    var idNum    = breakdown.month == 1 ? 'fid' : breakdown.month == 2 ? 'sid' : 'tid'
                    var monthNum = breakdown.month == 1 ? 'first' : breakdown.month == 2 ? 'second' : 'third'
                    breakdownForm[idNum]    = breakdown.id
                    breakdownForm[monthNum] = breakdown.number

                    var end = new Date().getTime();
                    time = end - start;
                    resolve(breakdownForm)
                }, time)
            })
            return p
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
        showIndicatorBreakdown(key, isSub = false){
            this.indicatorshow = false
            this.breakdownform = true
            this.breakdownkey = key
            this.breakdowns = (isSub === false) ? this.form.indicators[key].breakdowns : this.form.subs[isSub].indicators[key].breakdowns
        },
        totalIndicatorBreakdown(key, col, isSub = false){
            var result = 0
            var indicators = (isSub === false) ? this.form.indicators : this.form.subs[isSub].indicators
            if(indicators.length > 0){
                if(col != 'physical_targets'){
                    var quarter = col == 'first' ? 1 : col == 'second' ? 2 : col == 'third' ? 3 : 4
                    var breakdown = indicators[key].breakdowns.find(elem => elem.quarter == quarter)
                    for(let j = 0; j < this.breakdownmonths.length; j++){
                        var col = this.breakdownmonths[j]
                        result = result + this.strToFloat(breakdown[col])
                    }
                }
                else{
                    for(let i = 0; i < indicators[key].breakdowns.length; i++){
                        var breakdown = indicators[key].breakdowns[i]
                        for(let j = 0; j < this.breakdownmonths.length; j++){
                            var col = this.breakdownmonths[j]
                            result = result + this.strToFloat(breakdown[col])
                        }
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
                tag: null,
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
        // Form 2 - Outcome and Output
        submitFormTwo(){
            this.updateAnnexEOther(this.form2).then(res => {
                if(!res.errors){
                    this.syncRecords()
                    this.$refs.Close.click()
                }
                var icon = res.errors ? 'error' : 'success'
                this.toastMsg(icon, res.message)
            })
        },
        editFormTwo(indicator){
            var form = this.form2
            form.id = indicator.id
            form.type = indicator.type
            form.description = indicator.description
            this.setIndicatorDetail(form, indicator.details)

            form.subs = []
            for(let i = 0; i < indicator.subindicators.length; i++){
                var sub = indicator.subindicators[i]
                var temp = {
                    id: sub.id,
                    description: sub.description,
                    tag: null
                }
                this.setIndicatorDetail(temp, sub.details)
                form.subs.push(temp)
            }
        },
        setIndicatorDetail(array, details){
            for(let i = 0; i < this.indcols.length; i++){
                if(i != 0){
                    var col = this.indcols[i]
                    array[col] = details ? details[col] : ''
                }
            }
        },
        setFormLabel(row, num){
            if(row == 1){
                return num == 1 ? 'Actual' : num == 2 ? 'Estimate' : 'Physical Targets'
            }
            return num == 1 ? 'First' : num == 2 ? 'Second' : num == 3 ? 'Third' : 'Fourth'
        },
        setFormModel(row, num){
            if(row == 1){
                return num == 1 ? 'actual' : num == 2 ? 'estimate' : 'physical_targets'
            }
            return num == 1 ? 'first' : num == 2 ? 'second' : num == 3 ? 'third' : 'fourth'
        },
        // Button-User Validation
        checkUserTitle(status){
            var result = false
            var userTitle = this.authuser.active_profile.title.name
            
            if((status == 'New' || status == 'Draft') && (userTitle == 'Unit Head' || userTitle == 'Project Leader')){
                result = true
            }
            if(status == 'For Review' && userTitle == 'Unit Head'){
                result = true
            }
            if((status == 'For Approval' || status == 'Approved') && userTitle == 'Division Chief'){
                result = true
            }
            return result
        },
        checkUserDivision(project){
            var user = this.authuser
            var userObject = {
                division_id: user.division_id,
                unit_id:     user.unit_id,
                subunit_id:  user.subunit_id
            }
            var projectObject = {
                division_id: project.division_id,
                unit_id:     user.unit_id === null   ? null : project.unit_id,
                subunit_id:  user.subunit_id == null ? null : project.subunit_id
            }
            var userStr = JSON.stringify(userObject)
            var projStr = JSON.stringify(projectObject)

            return (userStr === projStr || user.active_profile.title.name == 'Superadmin') && this.isUserProjectLeader(project.leader.profile_id)
        },
        isUserProjectLeader(id){
            return id == this.authuser.active_profile.id
        },
        // Toast
        toastMsg(icon, msg){
            toast.fire({
                icon: icon,
                title: msg
            })
        },
        checkStatus(){
            var status = ''
            for(let i = 0; i < this.annexes.length; i++){
                var prog = this.annexes[i]
                for(let j = 0; j < prog.items.length; j++){
                    if(status == ''){
                        var item = prog.items[j]
                        status = item.status
                        break
                    }
                }
                for(let j = 0; j < prog.subprograms.length; j++){
                    var subp = prog.subprograms[j]
                    for(let k = 0; k < subp.items.length; k++){
                        var item = subp.items[k]
                        status = item.status
                        break
                    }
                    for(let k = 0; k < subp.clusters.length; k++){
                        var clus = subp.clusters[k]
                        for(let l = 0; l < clus.items.length; l++){
                            var item = clus.items[l]
                            status = item.status
                            break
                        }
                    }
                }
            }
            status = (status == '') ? 'New' : status
            this.displaystatus = status
            this.displaysyncstatus = status
        },
        setStatusIcon(status){
            return status == 'New' ? 'fa-sparkles text-warning' : 
            status == 'Draft' ? 'fa-edit' :
            status == 'For Review' ? 'fa-search' : 
            status == 'For Approval' ? 'fa-clipboard-list-check' : 
            status == 'Approved' ? 'fa-clipboard-check text-success' : 'fa-badge-check text-info'
        },
        setSubTitle(sub){
            return sub.subproject_id ? sub.subproject.title :
            sub.temp_title == 'ms' ? 'MS' :
            sub.temp_title == 'phd' ? 'PhD' : sub.temp_title
        },
        isForReview(status){
            return status != 'Draft' && status != 'New'
        },
        // history
        setHistory(item){
            this.histories = item.histories
            this.historyfor = item.project.title
        },
        // Watcher
        checkQueryStr(){
            this.fetchAnnexE(this.$route.query.id).then(res => {
                this.syncRecords(res.status)
                // console.log(res)
                this.editForm(res, 'details')
                this.editmode = true
            })
        }
    },
    computed: {
        ...mapGetters('user', ['getAuthUser']),
        authuser(){ return this.getAuthUser },
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
            this.loading = false
        })
        if(this.programs.length == 0){
            this.fetchPrograms()
        }
        if(this.divisions.length == 0){
            this.fetchDivisions()
        }
        if(!this.$route.query.id){
            this.checkStatus()
        }
    },
    watch: {
        $route: {
            immediate: true,
            handler (to, from) {
                if(this.$route.query.id){
                    this.checkQueryStr()
                }
            }
        },
    },
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
.min-100{
    min-width: 100px;
}
.minmax-2040{
    max-height: 40vh;
    min-height: 20vh;
}
</style>