<x-custom-admin-layout>
    <style>
        	.tab-container {
    display: flex;
    border-bottom: 1px solid #ccc;
    margin-bottom: 20px;
}

.tab-button {
    background-color: #f8f9fa;
    border: none;
    outline: none;
    cursor: pointer;
    padding: 10px 20px;
    font-size: 12.5px;
    transition: background-color 0.3s;
}

.tab-button:hover {
    background-color: #e9ecef;
}

.tab-button.active {
    font-weight: bold;
    color: #7360ff;
    background-color: #fff;
    border-bottom: 3px solid #7360ff;
}

.tab-button i {
    color: #667eea;
    font-size: 16px;
    transition: color 0.3s;
}

.tab-content {
    display: none;
    padding: 20px;
}
.tab-button.active i {
    color: #7360ff;
}
.tab-content.active {
    display: block;
}
        .custom-alert {
            position: fixed;
            top: 20px;
            right: 20px;
            min-width: 300px;
            z-index: 9999;
            transform: translateX(400px);
            transition: all 0.5s ease;
        }
        
        .custom-alert.show {
            transform: translateX(0);
        }
        
        .alert-success {
            animation: successPulse 1s ease-in-out;
        }
        
        @keyframes successPulse {
            0% { transform: scale(0.95); }
            50% { transform: scale(1.02); }
            100% { transform: scale(1); }
        }
        .action-buttons {
            padding: 1px;
            background: #f8f9fa;
            border-top: 1px solid #e9ecef;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .btn-enhanced {
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            border: none;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .btn-enhanced:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        
        .btn-draft {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
        }  

    </style>
    <div class="mobile-menu-overlay"></div>
    <div class="min-height-200px">
        <div class="pd-ltr-20 xs-pd-20-10">
			<div class="min-height-200px">
                <div class="tab-container" style="margin-top: -40px;">
    <button class="tab-button active" onclick="openTab(event, 'taborgstruct')">
        <i class="fas fa-sitemap"></i> Org Structure
    </button>
    <button class="tab-button" onclick="openTab(event, 'tabstatcodes')">
        <i class="fas fa-university"></i> Campus
    </button>
    <button class="tab-button" onclick="openTab(event, 'tabbranches')">
        <i class="fas fa-home"></i> Houses
    </button>
    <button class="tab-button" onclick="openTab(event, 'tabclass')">
        <i class="fas fa-chalkboard-teacher"></i> Classes
    </button>
    <button class="tab-button" id="tab-streams" onclick="openTab(event, 'tabstreams')">
        <i class="fas fa-project-diagram"></i> Streams
    </button>
    <button class="tab-button" onclick="openTab(event, 'tabperiod')">
        <i class="fas fa-calendar-alt"></i> Periods
    </button>
    <button class="tab-button" onclick="openTab(event, 'tabfcategories')">
        <i class="fas fa-tags"></i> Fee categories
    </button>
    <button class="tab-button" onclick="openTab(event, 'tabfpaymodes')">
        <i class="fas fa-credit-card"></i> Pay modes
    </button>
    <button class="tab-button" onclick="openTab(event, 'tabdepts')">
        <i class="fas fa-building"></i> Departments
    </button>
</div>
                
                <div id="status-message" class="alert alert-dismissible fade custom-alert" role="alert" style="display: none;">
                    <strong id="alert-title"></strong> <span id="alert-message"></span>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
                @endif
                <div id="taborgstruct" class="tab-content active" style="margin-top: -30px;">
                    <div class="row">
                        <div class="col-lg-4 col-md-6 col-sm-12 mb-30">
                            <div class="card-box pd-30 pt-10 height-100-p">
                                <h2 class="mb-30 h4">Organization Details</h2>
                                <section>
                                    <form  enctype="multipart/form-data" id="orgstrucf">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label >Name:</label>
                                                <input name="sname" type="text" class="form-control" required="true" autocomplete="off">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row"> 
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>Motto / Slogan:</label>
                                                <input name="motto" type="text" class="form-control" required="true" autocomplete="off">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>Logo:</label>
                                                <div class="custom-file">
                                                    <input name="file" id="file" type="file" class="custom-file-input" accept=".png,.jpg,.jpeg" onchange="validateFile('file')">
                                                    <label class="custom-file-label" for="file" id="selector">Upload Logo</label>
                                                    <span class="text-danger" id="file-error"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row"> 
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>P.O Box:</label>
                                                <input name="pobox" type="text" class="form-control" required="true" autocomplete="off">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>Email:</label>
                                                <input name="email" type="email" class="form-control" required="true" autocomplete="off">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>Address:</label>
                                                <input name="Address" type="text" class="form-control" required="true" autocomplete="off">
                                            </div>
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Save</button>
                                </form>
                                </section>
                            </div>
                        </div>
                        <div class="col-lg-8 col-md-6 col-sm-12 mb-30">
                            <div class="card-box pd-30 pt-10 height-100-p">
                                <div class="row">
                                    <h2 class="mb-30 h4">Organization Details</h2><br>
                                    <div class="pb-20">
                                        <table class="data-table table stripe hover nowrap">
                                        <thead>
                                            <tr>
                                                <th hidden>ID</th>
                                                <th>Name</th>
                                                <th>Logo</th>
                                                <th>Motto/Slogan</th>
                                                <th hidden>P.O. Box</th>
                                                <th hidden>Email</th>
                                                <th hidden>Address</th>
                                                <th class="datatable-nosort">Options</th>
                                            </tr>
                                        </thead>
                                        <tbody id="structure-table-body">

                                        </tbody>
                                    </table>
                                        
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="tabstatcodes" class="tab-content" style="margin-top: -30px;">
                    <div class="row">
                        <div class="col-lg-4 col-md-6 col-sm-12 mb-30">
                            <div class="card-box pd-30 pt-10 height-100-p">
                                <h2 class="mb-30 h4">Campuses</h2>
                                <section>
                                    <form id="campusform" >
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label >Name:</label> 
                                                <input name="branchname" id="branchname" type="text" class="form-control" required="true" autocomplete="off">
                                            </div>
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Save</button>
                                </form>
                                </section>
                            </div>
                        </div>
                        <div class="col-lg-8 col-md-6 col-sm-12 mb-30">
                            <div class="card-box pd-30 pt-10 height-100-p">
                                <div class="row">
                                    <h2 class="mb-30 h4">Campuses</h2><br>
                                    <div class="pb-20">
                                        <table class="data-table table stripe hover nowrap">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Name</th>
                                            <th class="datatable-nosort">Options</th>
                                        </tr>
                                    </thead>
                                    <tbody id="campuses-table-body"></tbody>
                                </table>
                                <div id="pagination-controls" class="mt-3"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="tabfcategories" class="tab-content" style="margin-top: -30px;">
                    <div class="row">
                        <div class="col-lg-4 col-md-6 col-sm-12 mb-30">
                            <div class="card-box pd-30 pt-10 height-100-p">
                                <h2 class="mb-30 h4">Fee categories</h2>
                                <section>
                                    <form id="fcategoriesform" >
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label >Category:</label> 
                                                <input name="catename" id="catename" type="text" class="form-control" required="true" autocomplete="off">
                                            </div>
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Save</button>
                                </form>
                                </section>
                            </div>
                        </div>
                        <div class="col-lg-8 col-md-6 col-sm-12 mb-30">
                            <div class="card-box pd-30 pt-10 height-100-p">
                                <div class="row">
                                    <h2 class="mb-30 h4">Fee categories</h2><br>
                                    <div class="pb-20">
                                        <table class="data-table table stripe hover nowrap">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Name</th>
                                            <th class="datatable-nosort">Options</th>
                                        </tr>
                                    </thead>
                                    <tbody id="fcategories-table-body"></tbody>
                                </table>
                                <div id="pagination-fcategories" class="mt-3"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="tabfpaymodes" class="tab-content" style="margin-top: -30px;">
                    <div class="row">
                        <div class="col-lg-4 col-md-6 col-sm-12 mb-30">
                            <div class="card-box pd-30 pt-10 height-100-p">
                                <h2 class="mb-30 h4">Payment Modes</h2>
                                <section>
                                    <form id="pmodesform" >
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label >Name:</label> 
                                                <input name="pname" id="pname" type="text" class="form-control" required="true" autocomplete="off">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label >Status:</label> 
                                                <select name="sstatus" id="sstatus" class="custom-select form-control" required autocomplete="off">
                                                    <option value="">Select Status</option>
                                                    <option value="Active">Active</option>
                                                    <option value="Inactive">Inactive</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label >Requires:</label>
                                            <div class="checkbox-container flex">
                                                <div class="form-check form-check-inline">
                                                    <input type="checkbox" id="tcode" name="tcode" value="Yes" class="form-check-input">
                                                    <label for="tcode" class="form-check-label">Transaction code</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input type="checkbox" id="chequeno" name="chequeno" value="Yes" class="form-check-input">
                                                    <label for="chequeno" class="form-check-label">Cheque No.</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input type="checkbox" id="bankn" name="bankn" value="Yes" class="form-check-input">
                                                    <label for="bankn" class="form-check-label">Bank</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Save</button>
                                </form>
                                </section>
                            </div>
                        </div>
                        <div class="col-lg-8 col-md-6 col-sm-12 mb-30">
                            <div class="card-box pd-30 pt-10 height-100-p">
                                <div class="row">
                                    <h2 class="mb-30 h4">Pay Methods</h2><br>
                                    <div class="pb-20">
                                        <table class="data-table table stripe hover nowrap">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Method</th>
                                            <th>Status</th>
                                            <th>T code</th>
                                            <th>Cheque No</th>
                                            <th>Bank</th>
                                            <th class="datatable-nosort">Options</th>
                                        </tr>
                                    </thead>
                                    <tbody id="pmodes-table-body"></tbody>
                                </table>
                                <div id="pagination-pmodes" class="mt-3"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="tabbranches" class="tab-content" style="margin-top: -30px;">
                    <div class="row">
                        <div class="col-lg-4 col-md-6 col-sm-12 mb-30">
                            <div class="card-box pd-30 pt-10 height-100-p">
                                <h2 class="mb-30 h4">Houses</h2>
                                <section>
                                    <form id="housesform" >
                                    @csrf
                                    
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label >Campus:</label>
                                                <select name="branch2" id="branch2" class="custom-select form-control" required>
                                                    
                                                </select>

                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label >Name:</label> 
                                                <input name="housename" id="housename" type="text" class="form-control" required="true" autocomplete="off">
                                            </div>
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Save</button>
                                </form>
                                </section>
                            </div>
                        </div>
                        <div class="col-lg-8 col-md-6 col-sm-12 mb-30">
                            <div class="card-box pd-30 pt-10 height-100-p">
                                <div class="row">
                                    <h2 class="mb-30 h4">Houses</h2><br>
                                    <div class="pb-20">
                                        <table class="data-table table stripe hover nowrap">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Campus</th>
                                            <th>House</th>
                                            <th class="datatable-nosort">Options</th>
                                        </tr>
                                    </thead>
                                    <tbody id="houses-table-body"></tbody>
                                </table>
                                <div id="pagination-controls2" class="mt-3"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="tabperiod" class="tab-content" style="margin-top: -30px;">
                    <div class="row">
                        <div class="col-lg-4 col-md-6 col-sm-12 mb-30">
                            <div class="card-box pd-30 pt-10 height-100-p">
                                <h2 class="mb-30 h4">Periods</h2>
                                <section>
                                    <form id="periodsform" >
                                    @csrf
                                    
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label >Period name:</label>
                                                <input name="periodname" id="periodname" type="text" class="form-control" required="true" autocomplete="off">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label >Status:</label> 
                                                <select name="pstatus" id="pstatus" class="custom-select form-control" required autocomplete="off">
                                                    <option value="">Select Status</option>
                                                    <option value="Active">Active</option>
                                                    <option value="Inactive">Inactive</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label >Start Date:</label>
                                                <input name="startdate" id="startdate" type="date" class="form-control" required="true" autocomplete="off">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label >End Date:</label>
                                                <input name="enddate" id="enddate" type="date" class="form-control" required="true" autocomplete="off">
                                            </div>
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Save</button>
                                </form>
                                </section>
                            </div>
                        </div>
                        <div class="col-lg-8 col-md-6 col-sm-12 mb-30">
                            <div class="card-box pd-30 pt-10 height-100-p">
                                <div class="row">
                                    <h2 class="mb-30 h4">Periods</h2><br>
                                    <div class="pb-20">
                                        <table class="data-table table stripe hover nowrap">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Period</th>
                                            <th>Status</th>
                                            <th>Start</th>
                                            <th>End</th>
                                            <th class="datatable-nosort">Options</th>
                                        </tr>
                                    </thead>
                                    <tbody id="periods-table-body"></tbody>
                                </table>
                                <div id="pagination-controlsperiod" class="mt-3"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="tabdepts" class="tab-content" style="margin-top: -30px;">
                    <div class="row">
                        <div class="col-lg-4 col-md-6 col-sm-12 mb-30">
                            <div class="card-box pd-30 pt-10 height-100-p">
                                <h2 class="mb-30 h4">Departments</h2>
                                <section>
                                    <form id="deptsform" >
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label >Department Name:</label> 
                                                <input name="deptname" id="deptname" type="text" class="form-control" required="true" autocomplete="off">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label >Head of Department:</label> 
                                                 <select name="HOD" id="HOD" class="custom-select form-control" required>
                                                 </select>
                                                 <small id="HOD-error" class="text-danger"></small>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <button type="submit" class="btn btn-primary">Save</button>
                                </form>
                                </section>
                            </div>
                        </div>
                        <div class="col-lg-8 col-md-6 col-sm-12 mb-30">
                            <div class="card-box pd-30 pt-10 height-100-p">
                                <div class="row">
                                    <h2 class="mb-30 h4">Departments</h2><br>
                                    <div class="pb-20">
                                        <table class="data-table table stripe hover nowrap">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Name</th>
                                            <th>HOD</th>
                                            <th class="datatable-nosort">Options</th>
                                        </tr>
                                    </thead>
                                    <tbody id="depts-table-body"></tbody>
                                </table>
                                <div id="pagination-depts" class="mt-3"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="tabstreams" class="tab-content" style="margin-top: -30px;">
                    <div class="row">
                        <div class="col-lg-4 col-md-6 col-sm-12 mb-30">
                            <div class="card-box pd-30 pt-10 height-100-p">
                                <h2 class="mb-30 h4">Streams</h2>
                                <form id="streamform">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>Class:</label> 
                                               
                                                <select name="streamclass" id="streamclass" class="form-select" required>
                                                    
                                                </select>
                                                <small id="streamclass-error" class="text-danger"></small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>Stream Name:</label> 
                                                <input name="strmname" id="strmname" type="text" class="form-control" required autocomplete="off">
                                                <small id="strmname-error" class="text-danger"></small> <!-- Error message placeholder -->
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>Class Teacher:</label> 
                                               
                                                <select name="streamteach" id="streamteach" class="form-select" required>
                                                    
                                                </select>
                                                <small id="streamteach-error" class="text-danger"></small>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <button type="submit" class="btn btn-enhanced btn-draft">
                                        <i class="fas fa-save"></i>Save
                                    </button>
                                </form>
                            </div>
                        </div>
                         
                        <div class="col-lg-8 col-md-6 col-sm-12 mb-30">
                            <div class="card-box pd-30 pt-10 height-100-p">
                                <div class="row">
                                    <h2 class="mb-30 h4">Streams</h2><br>
                                    <div class="pb-20">
                                        <table class="data-table table stripe hover nowrap">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Name</th>
                                                    <th class="datatable-nosort">Options</th>
                                                </tr>
                                            </thead>
                                            <tbody id="stream-table-body"></tbody>
                                        </table>
                                        <div id="pagination-controls3" class="mt-3"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="tabclass" class="tab-content" style="margin-top: -30px;">
                    <div class="row">
                        <div class="col-lg-4 col-md-6 col-sm-12 mb-30">
                            <div class="card-box pd-30 pt-10 height-100-p">
                                <h2 class="mb-30 h4">Classes</h2>
                                <section>
                                <form id="classform">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>Campus:</label> 
                                                
                                                <select name="caid" id="branch4" class="custom-select form-control" required>
                                                    
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>Class name:</label> 
                                                <input name="claname" id="claname" type="text" class="form-control" required autocomplete="off">
                                                <small id="claname-error" class="text-danger"></small> <!-- Error message -->
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>Rank:</label> 
                                                <input name="clarank" id="clarank" type="number" class="form-control" required autocomplete="off">
                                                <small id="clarank-error" class="text-danger"></small> <!-- Error message -->
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>Class Head:</label> 
                                               
                                                <select name="clateach" id="clateach" class="form-select" required>
                                                    
                                                </select>
                                                <small id="clateach-error" class="text-danger"></small>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <button type="submit" class="btn btn-enhanced btn-draft">
                                        <i class="fas fa-save"></i>Save
                                    </button>
                                </form>

                                </section>
                            </div>
                        </div>
                        <div class="col-lg-8 col-md-6 col-sm-12 mb-30">
                            <div class="card-box pd-30 pt-10 height-100-p">
                                <div class="row">
                                    <h2 class="mb-30 h4">Classes</h2><br>
                                    <div class="pb-20">
                                        <table class="data-table table stripe hover nowrap">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Stream</th>
                                                    <th>Class</th>
                                                    <th>Rank</th>
                                                    <th>C.Teacher</th>
                                                    <th class="datatable-nosort">Options</th>
                                                </tr>
                                            </thead>
                                            <tbody id="classes-table-body"></tbody>
                                        </table>
                                        <div id="pagination-controls4" class="mt-3"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
               
                
            </div>
        </div>
    <div class="modal fade" id="editSchoolModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit School Information</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="editSchoolForm" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" id="ID" name="id">
                    
                    <div class="row">
                        <div class="col-md-6 col-sm-12">
                            <div class="form-group">
                                <label for="schoolName">Name:</label>
                                <input type="text" class="form-control" id="schoolName" name="name">
                                <span class="text-danger" id="name-error"></span>
                            </div>
                        </div>
                        <div class="col-md-6 col-sm-12">
                            <div class="form-group">
                                <label for="schoolMotto">Motto:</label>
                                <input type="text" class="form-control" id="schoolMotto" name="motto">
                                <span class="text-danger" id="motto-error"></span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="schoolPobox">P.O Box:</label>
                        <input type="text" class="form-control" id="schoolPobox" name="pobox">
                        <span class="text-danger" id="pobox-error"></span>
                    </div>
                    <div class="row">
                        <div class="col-md-6 col-sm-12">
                            <div class="form-group">
                                <label for="schoolEmail">Email:</label>
                                <input type="email" class="form-control" id="schoolEmail" name="email">
                                <span class="text-danger" id="email-error"></span>
                            </div>
                        </div>
                        <div class="col-md-6 col-sm-12">
                            <div class="form-group">
                                <label for="schoolPhysaddres">Address:</label>
                                <input type="text" class="form-control" id="schoolPhysaddres" name="physaddres">
                                <span class="text-danger" id="physaddres-error"></span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="schoolLogo">Logo:</label>
                        <input type="file" class="form-control" id="schoolLogo" name="logo">
                        <img id="schoolLogoPreview" src="" alt="School Logo" style="max-width: 100px; margin-top: 10px;">
                        <span class="text-danger" id="logo-error"></span>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" form="editSchoolForm" class="btn btn-primary">Save changes</button>
                    </div>
                </form>
            </div>
           
        </div>
    </div>
</div>
<div class="modal fade" id="editstreamModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Stream</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="editstreamForm">
                    @csrf
                    <input type="hidden" id="ID" name="id">
                    
                
                    <div class="form-group">
                        <label for="schoolPobox">Name:</label>
                        <input type="text" class="form-control" id="editstrmname" name="strmname">
                        <span class="text-danger" id="strmname-error"></span>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" form="editstreamForm" class="btn btn-primary">Save changes</button>
                    </div>
                </form>
            </div>
            
        </div>
    </div>
</div>
<div class="modal fade" id="editcampusModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Campus</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="editcampuslForm">
                    @csrf
                    <input type="hidden" id="ID" name="id">
                    
                
                    <div class="form-group">
                        <label for="schoolPobox">Name:</label>
                        <input type="text" class="form-control" id="editbranchname" name="branchname">
                        <span class="text-danger" id="branchname-error"></span>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" form="editcampuslForm" class="btn btn-primary">Save changes</button>
                    </div>
                </form>
            </div>
            
        </div>
    </div>
</div>
<div class="modal fade" id="edithouseModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit House</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="edithouseForm">
                    @csrf
                    <input type="hidden" id="ID" name="id">
                    
                
                    <div class="form-group">
                        <div class="form-group">
                            <label >Campus:</label>
                            <select name="brid" id="branch3" class="custom-select form-control" required>
                                
                            </select>
                        </div>
                        <label for="schoolPobox">Name:</label>
                        <input type="text" class="form-control" id="edithousename" name="housen">
                        <span class="text-danger" id="housename-error"></span>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" form="edithouseForm" class="btn btn-primary">Save changes</button>
                    </div>
                </form>
            </div>
            
        </div>
    </div>
</div>
<div class="modal fade" id="editclassModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit class Information</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="editclaForm">
                    @csrf
                    <input type="hidden" id="ID" name="id">
                    
                    <div class="row">
                        <div class="col-md-6 col-sm-12">
                            <div class="form-group">
                                <label for="schoolName">Stream:</label>
                               
                                <select name="stid" id="streamd2" class="custom-select form-control" required>

                                                </select>
                                <span class="text-danger" id="stid-error"></span>
                            </div>
                        </div>
                        <div class="col-md-6 col-sm-12">
                            <div class="form-group">
                                <label for="schoolMotto">Class:</label>
                                <input type="text" class="form-control" id="editcla" name="claname">
                                <span class="text-danger" id="claname-error"></span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 col-sm-12">
                            <div class="form-group">
                                <label for="schoolPobox">Rank:</label>
                                <input type="text" class="form-control" id="editrank" name="clarank">
                                <span class="text-danger" id="clarank-error"></span>
                            </div>
                        </div>
                        <div class="col-md-6 col-sm-12">
                            <div class="form-group">
                                <label for="schoolEmail">Class teacher:</label>
                                <input type="text" class="form-control" id="editclateach" name="clateach">
                                <span class="text-danger" id="clateach-error"></span>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" form="editclaForm" class="btn btn-primary">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="editpmodeModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Payment method</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="editpmodesForm">
                    @csrf
                    <input type="hidden" id="ID" name="id">
                    
                    <div class="row">
                        <div class="col-md-6 col-sm-12">
                            <div class="form-group">
                                <label for="schoolName">Name:</label>
                               
                                <input name="pname" id="epmoden" class="form-control" required>
                                <span class="text-danger" id="pname-error"></span>
                            </div>
                        </div>
                        <div class="col-md-6 col-sm-12">
                            <div class="form-group">
                                <label for="schoolName">Status:</label>
                                 <select name="sstatus" id="editsstatus" class="custom-select form-control" required autocomplete="off">
                                 <option value="">Select Status</option>
                                 <option value="Active">Active</option>
                                 <option value="Inactive">Inactive</option>
                                 </select>
                                <span class="text-danger" id="sstatus-error"></span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                                        <div class="form-group">
                                            <label >Requires:</label>
                                            <div class="checkbox-container flex">
                                                <div class="form-check form-check-inline">
                                                    <input type="checkbox" id="edittcode" name="tcode" value="Yes" class="form-check-input">
                                                    <label for="edittcode" class="form-check-label">Transaction code</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input type="checkbox" id="editchequeno" name="chequeno" value="Yes" class="form-check-input">
                                                    <label for="editchequeno" class="form-check-label">Cheque No.</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input type="checkbox" id="editbankn" name="bankn" value="Yes" class="form-check-input">
                                                    <label for="editbankn" class="form-check-label">Bank</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" form="editpmodesForm" class="btn btn-primary">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<script src="{{ asset('src/plugins/sweetalert2/sweetalert2.all.js') }}"></script>
<script src="{{ asset('src/plugins/sweetalert2/sweet-alert.init.js') }}"></script>

    <script>
        function openTab(evt, tabName) { 
    var i, tabContent, tabButton;

    // Hide all tab content
    tabContent = document.getElementsByClassName("tab-content");
    for (i = 0; i < tabContent.length; i++) {
        tabContent[i].style.display = "none";
    }

    // Remove the "active" class from all tab buttons
    tabButton = document.getElementsByClassName("tab-button");
    for (i = 0; i < tabButton.length; i++) {
        tabButton[i].className = tabButton[i].className.replace(" active", "");
    }

    // Show the current tab and add an "active" class to the button that opened the tab
    document.getElementById(tabName).style.display = "block";
    evt.currentTarget.className += " active";
}
        $(document).ready(function() {
           
            loadTableData();
            loadcampuses();
            loadhouses();
            loadstreams();
            loadclasses();
            loadperiods();
            loadfcategories();
            loadpmodes();

            $('#orgstrucf').on('submit', function(e) {
                e.preventDefault();
                $('.text-danger').html('');
                let formData = new FormData(this);

                const submitBtn = $(this).find('button[type="submit"]');
                const originalText = submitBtn.html();
                submitBtn.html('<i class="fa fa-spinner fa-spin"></i> Saving...').prop('disabled', true);
                
                $.ajax({
                    url: "{{ route('staticinfo.store') }}",
                    type: "POST",
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        showAlert('success', 'Success!', response.message);
                        $('#orgstrucf')[0].reset();
                        loadTableData();
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            $.each(errors, function(key, value) {
                                $('#' + key + '-error').html(value[0]);
                            });
                            showAlert('danger', 'Error!', 'Please check the form for errors.');
                        } else {
                            showAlert('danger', 'Error!', 'Error adding student');
                        }
                    },
                    complete: function() {
                        submitBtn.html(originalText).prop('disabled', false);
                    }
                });
            });
            $('#fcategoriesform').on('submit', function(e) { 
                e.preventDefault();
                $('.text-danger').html('');
                let formData = new FormData(this);
                
                const submitBtn = $(this).find('button[type="submit"]');
                const originalText = submitBtn.html();
                submitBtn.html('<i class="fa fa-spinner fa-spin"></i> Saving...').prop('disabled', true);
                
                $.ajax({
                    url: "{{ route('fcategories.store') }}",
                    type: "POST",
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        showAlert('success', 'Success!', response.message);
                        $('#fcategoriesform')[0].reset();
                        loadfcategories();
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            $.each(errors, function(key, value) {
                                $('#' + key + '-error').html(value[0]);
                            });
                            showAlert('danger', 'Error!', 'Please check the form for errors.');
                        } else {
                            showAlert('danger', 'Error!', 'Error adding student');
                        }
                    },
                    complete: function() {
                        submitBtn.html(originalText).prop('disabled', false);
                    }
                });
            });
            $('#pmodesform').on('submit', function(e) { 
    e.preventDefault();
    $('.text-danger').html('');

    // Ensure unchecked checkboxes are submitted with value 'No'
    ['tcode', 'chequeno', 'bankn'].forEach(function(field) {
        if (!$('[name="' + field + '"]').is(':checked')) {
            if (!$('[name="' + field + '"]').length) {
                // If checkbox not present, add hidden field
                $('<input>').attr({
                    type: 'hidden',
                    name: field,
                    value: 'No'
                }).appendTo('#pmodesform');
            } else {
                // Add hidden input with "No" if not checked
                $('#pmodesform').append(
                    $('<input>').attr({
                        type: 'hidden',
                        name: field,
                        value: 'No'
                    })
                );
            }
        }
    });

    let formData = new FormData(this);
    const submitBtn = $(this).find('button[type="submit"]');
    const originalText = submitBtn.html();
    submitBtn.html('<i class="fa fa-spinner fa-spin"></i> Saving...').prop('disabled', true);

    $.ajax({
        url: "{{ route('pmodes.store') }}",
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,
        success: function(response) {
            showAlert('success', 'Success!', response.message);
            $('#pmodesform')[0].reset();
            loadpmodes();
        },
        error: function(xhr) {
            if (xhr.status === 422) {
                let errors = xhr.responseJSON.errors;
                $.each(errors, function(key, value) {
                    $('#' + key + '-error').html(value[0]);
                });
                showAlert('danger', 'Error!', 'Please check the form for errors.');
            } else {
                showAlert('danger', 'Error!', 'Error adding pay mode');
            }
        },
        complete: function() {
            submitBtn.html(originalText).prop('disabled', false);
        }
    });
});
$.ajax({
    url: "{{ route('teachers.getDropdown') }}",
    type: "GET",
    success: function (response) {
        const dropdown = $('#clateach');
        dropdown.empty();
        dropdown.append('<option value="">Select Teacher</option>');
        response.data.forEach(function (teachers) {
            dropdown.append(
                `<option value="${teachers.ID}">${teachers.teachername}</option>`
            );
        });
        dropdown.select2({
            placeholder: "Select Teacher",
            allowClear: true,
            width: '100%'
        });
    },
    error: function () {
        alert('Failed to load Classes. Please try again.');
    },
});
$('#editpmodesForm').on('submit', function (e) {  
    e.preventDefault();
    const form = $(this);
    const id = $('#editpmodeModal #ID').val(); // Fetch the ID value

    // Remove previous hidden fallback inputs (avoid duplicates)
    form.find('input[type="hidden"].checkbox-fallback').remove();

    // Ensure unchecked checkboxes submit "No"
    ['tcode', 'chequeno', 'bankn'].forEach(function (field) {
        if (!$('[name="' + field + '"]').is(':checked')) {
            $('<input>').attr({
                type: 'hidden',
                name: field,
                value: 'No',
                class: 'checkbox-fallback'
            }).appendTo(form);
        }
    });

    const formData = new FormData(this);
    formData.append('_method', 'POST'); // Simulating PUT

    const submitBtn = form.find('button[type="submit"]');
    const originalText = submitBtn.html();
    submitBtn.html('<i class="fa fa-spinner fa-spin"></i> Updating...').prop('disabled', true);

    $.ajax({
        url: `{{ url('pmodes') }}/${id}`, // Adjusted correctly
        type: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (response) {
            showAlert('success', 'Success!', response.message);
            $('#editpmodeModal').modal('hide');
            form[0].reset();
            loadpmodes(); // Reload the table
        },
        error: function (xhr) {
            if (xhr.status === 422) {
                let errors = xhr.responseJSON.errors;
                $.each(errors, function (key, value) {
                    $(`#${key}-error`).html(value[0]);
                });
                showAlert('danger', 'Error!', 'Please check the form for errors.');
            } else {
                showAlert('danger', 'Error!', 'Error updating pay mode.');
            }
        },
        complete: function () {
            submitBtn.html(originalText).prop('disabled', false);
        }
    });
});

            $('#campusform').on('submit', function(e) { 
                e.preventDefault();
                $('.text-danger').html('');
                let formData = new FormData(this);
                
                const submitBtn = $(this).find('button[type="submit"]');
                const originalText = submitBtn.html();
                submitBtn.html('<i class="fa fa-spinner fa-spin"></i> Saving...').prop('disabled', true);
                
                $.ajax({
                    url: "{{ route('branches.store') }}",
                    type: "POST",
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        showAlert('success', 'Success!', response.message);
                        $('#campusform')[0].reset();
                        loadcampuses();
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            $.each(errors, function(key, value) {
                                $('#' + key + '-error').html(value[0]);
                            });
                            showAlert('danger', 'Error!', 'Please check the form for errors.');
                        } else {
                            showAlert('danger', 'Error!', 'Error adding student');
                        }
                    },
                    complete: function() {
                        submitBtn.html(originalText).prop('disabled', false);
                    }
                });
            });
            $('#classform').on('submit', function(e) {
    e.preventDefault();
    $('.text-danger').html(''); // Clear previous errors
    let formData = new FormData(this);

    const submitBtn = $(this).find('button[type="submit"]');
    const originalText = submitBtn.html();
    submitBtn.html('<i class="fa fa-spinner fa-spin"></i> Saving...').prop('disabled', true);

    $.ajax({
        url: "{{ route('classes.store') }}",
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,
        success: function(response) {
            showAlert('success', 'Success!', response.message);
            $('#classform')[0].reset();
            loadClasses(); // Refresh class list
        },
        error: function(xhr) {
            if (xhr.status === 422) {
                let errors = xhr.responseJSON.errors;
                $.each(errors, function(key, value) {
                    $('#' + key + '-error').html(value[0]); // Show error message under the input
                });
                showAlert('danger', 'Error!', 'Please check the form for errors.');
            } else {
                showAlert('danger', 'Error!', 'Error adding class');
            }
        },
        complete: function() {
            submitBtn.html(originalText).prop('disabled', false);
        }
    });
});

            $('#streamform').on('submit', function(e) {
                e.preventDefault();
                $('.text-danger').html('');
                let formData = new FormData(this);
                
                const submitBtn = $(this).find('button[type="submit"]');
                const originalText = submitBtn.html();
                submitBtn.html('<i class="fa fa-spinner fa-spin"></i> Saving...').prop('disabled', true);
                
                $.ajax({
                    url: "{{ route('streams.store') }}",
                    type: "POST",
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        showAlert('success', 'Success!', response.message);
                        $('#streamform')[0].reset();
                        $('#streamclass').val('');
                        $('#streamteach').val('');
                        loadstreams();
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            $.each(errors, function(key, value) {
                                $('#' + key + '-error').html(value[0]);
                            });
                            showAlert('danger', 'Error!', 'Please check the form for errors.');
                        } else {
                            showAlert('danger', 'Error!', 'Error adding student');
                        }
                    },
                    complete: function() {
                        submitBtn.html(originalText).prop('disabled', false);
                    }
                });
            });
            $('#deptsform').on('submit', function(e) { 
                e.preventDefault();
                $('.text-danger').html('');
                let formData = new FormData(this);
                
                const submitBtn = $(this).find('button[type="submit"]');
                const originalText = submitBtn.html();
                submitBtn.html('<i class="fa fa-spinner fa-spin"></i> Saving...').prop('disabled', true);
                
                $.ajax({
                    url: "{{ route('depts.store') }}",
                    type: "POST",
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        showAlert('success', 'Success!', response.message);
                        $('#deptsform')[0].reset();
                        //loadfcategories();
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            $.each(errors, function(key, value) {
                                $('#' + key + '-error').html(value[0]);
                            });
                            showAlert('danger', 'Error!', 'Please check the form for errors.');
                        } else {
                            showAlert('danger', 'Error!', 'Error adding student');
                        }
                    },
                    complete: function() {
                        submitBtn.html(originalText).prop('disabled', false);
                    }
                });
            });
            $('#periodsform').on('submit', function(e) {
                e.preventDefault();
                $('.text-danger').html('');
                let formData = new FormData(this);
                
                const submitBtn = $(this).find('button[type="submit"]');
                const originalText = submitBtn.html();
                submitBtn.html('<i class="fa fa-spinner fa-spin"></i> Saving...').prop('disabled', true);
                
                $.ajax({
                    url: "{{ route('periods.store') }}",
                    type: "POST",
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        showAlert('success', 'Success!', response.message);
                        $('#periodsform')[0].reset();
                        loadperiods();
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            $.each(errors, function(key, value) {
                                $('#' + key + '-error').html(value[0]);
                            });
                            showAlert('danger', 'Error!', 'Please check the form for errors.');
                        } else {
                            showAlert('danger', 'Error!', 'Error adding student');
                        }
                    },
                    complete: function() {
                        submitBtn.html(originalText).prop('disabled', false);
                    }
                });
            });
            $('#housesform').on('submit', function(e) {
                e.preventDefault();
                $('.text-danger').html('');
                let formData = new FormData(this);
                
                const submitBtn = $(this).find('button[type="submit"]');
                const originalText = submitBtn.html();
                submitBtn.html('<i class="fa fa-spinner fa-spin"></i> Saving...').prop('disabled', true);
                
                $.ajax({
                    url: "{{ route('houses.store') }}",
                    type: "POST",
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        showAlert('success', 'Success!', response.message);
                        $('#housesform')[0].reset();
                        loadhouses();
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            $.each(errors, function(key, value) {
                                $('#' + key + '-error').html(value[0]);
                            });
                            showAlert('danger', 'Error!', 'Please check the form for errors.');
                        } else {
                            showAlert('danger', 'Error!', 'Error adding student');
                        }
                    },
                    complete: function() {
                        submitBtn.html(originalText).prop('disabled', false);
                    }
                });
            });
            function showAlert(type, title, message) {
                const statusMessage = $('#status-message');
                $('#alert-title').html(title);
                $('#alert-message').html(message);
                
                statusMessage
                    .removeClass('alert-success alert-danger')
                    .addClass(`alert-${type}`)
                    .css('display', 'block')
                    .addClass('show');
                
                // Auto hide after 5 seconds if not manually closed
                setTimeout(() => {
                    if (statusMessage.hasClass('show')) {
                        statusMessage.removeClass('show');
                        setTimeout(() => {
                            statusMessage.hide();
                        }, 500);
                    }
                }, 5000);
            }
            $('.close').on('click', function() {
                const alert = $(this).closest('.custom-alert');
                alert.removeClass('show');
                setTimeout(() => {
                    alert.hide();
                }, 500);
            });
            $(document).on('click', '[data-target="#editSchoolModal"]', function () { 
    const id = $(this).data('id');
    const name = $(this).data('name');
    const motto = $(this).data('motto');
    const pobox = $(this).data('pobox');
    const email = $(this).data('email');
    const physaddres = $(this).data('physaddres');
    const logo = $(this).data('logo');

    // Clear previous errors
    $('.text-danger').html('');
    
    // Set form values
    const form = $('#editSchoolForm');
    form.find('#ID').val(id);
    form.find('#schoolName').val(name);
    form.find('#schoolMotto').val(motto);
    form.find('#schoolPobox').val(pobox);
    form.find('#schoolEmail').val(email);
    form.find('#schoolPhysaddres').val(physaddres);
    form.find('#schoolLogoPreview').attr('src', logo);
});
$(document).on('click', '[data-target="#editpmodeModal"]', function () { 
    const id = $(this).data('id');
    const pname = $(this).data('pname');
    const sstatus = $(this).data('sstatus');
    const tcode = $(this).data('tcode');
    const chequeno = $(this).data('chequeno');
    const bankn = $(this).data('bankn');

    // Clear previous errors
    $('.text-danger').html('');

    // Set form values
    const form = $('#editpmodesForm');
    form.find('#ID').val(id);
    form.find('#epmoden').val(pname);
    form.find('#editsstatus').val(sstatus);

    // Set checkbox state
    form.find('#edittcode').prop('checked', tcode === 'Yes');
    form.find('#editchequeno').prop('checked', chequeno === 'Yes');
    form.find('#editbankn').prop('checked', bankn === 'Yes');
});

$(document).on('click', '[data-target="#editcampusModal"]', function () {
    const id = $(this).data('id');
    const branchname = $(this).data('branchname');
    

    // Clear previous errors
    $('.text-danger').html('');
    
    // Set form values
    const form = $('#editcampuslForm');
    form.find('#ID').val(id);
    form.find('#editbranchname').val(branchname);
   
});
$(document).on('click', '[data-target="#editstreamModal"]', function () {
    const id = $(this).data('id');
    const strmname = $(this).data('strmname');
    

    // Clear previous errors
    $('.text-danger').html('');
    
    // Set form values
    const form = $('#editstreamForm');
    form.find('#ID').val(id);
    form.find('#editstrmname').val(strmname);
   
});
$(document).on('click', '[data-target="#edithouseModal"]', function () {
    const id = $(this).data('id');
    
    const housen = $(this).data('housen');
    const brid = $(this).data('brid');
    

    // Clear previous errors
    $('.text-danger').html('');
    
    // Set form values
    const form = $('#edithouseForm');
    form.find('#ID').val(id);
    form.find('#edithousename').val(housen);
    form.find('#branch3').val(brid);
   
});
$(document).on('click', '[data-target="#editclassModal"]', function () { 
    const id = $(this).data('id');
    const stid = $(this).data('stid');
    const claname = $(this).data('claname');
    const clarank = $(this).data('clarank');
    const clateach = $(this).data('clateach');

    // Clear previous errors
    $('.text-danger').html('');
    
    // Set form values
    const form = $('#editclaForm');
    form.find('#ID').val(id);
    form.find('#streamd2').val(stid);
    form.find('#editcla').val(claname);
    form.find('#editrank').val(clarank);
    form.find('#editclateach').val(clateach);
});           
            
            $('#editSchoolForm').on('submit', function (e) {
                e.preventDefault();
                const id = $('#editSchoolModal #ID').val(); // Fetch the ID value correctly
                const formData = new FormData(this);

                const submitBtn = $(this).find('button[type="submit"]');
                const originalText = submitBtn.html();
                submitBtn.html('<i class="fa fa-spinner fa-spin"></i> Updating...').prop('disabled', true);
                
               
                formData.append('_method', 'POST');
                $.ajax({
                    url: `{{ url('static') }}/${id}`, // Adjust route as needed
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (response) {
                        showAlert('success', 'Success!', response.message);
                        $('#editSchoolModal').modal('hide');
                        loadTableData(); // Reload the table
                        // 
                    },
                    error: function (xhr) {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            $.each(errors, function (key, value) {
                                $(`#${key}-error`).html(value[0]);
                            });
                            showAlert('danger', 'Error!', 'Please check the form for errors.');
                        } else {
                            showAlert('danger', 'Error!', 'Error updating organization info.');
                        }
                    },
                    complete: function() {
                        submitBtn.html(originalText).prop('disabled', false);
                    }
                });
            }); 
            $('#editcampuslForm').on('submit', function (e) {
                e.preventDefault();
                const id = $('#editcampusModal #ID').val(); // Fetch the ID value correctly
                const formData = new FormData(this);

                const submitBtn = $(this).find('button[type="submit"]');
                const originalText = submitBtn.html();
                submitBtn.html('<i class="fa fa-spinner fa-spin"></i> Updating...').prop('disabled', true);
                
               
                formData.append('_method', 'POST');
                $.ajax({
                    url: `{{ url('branches') }}/${id}`, // Adjust route as needed
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (response) {
                        showAlert('success', 'Success!', response.message);
                        $('#editcampusModal').modal('hide');
                        loadcampuses(); // Reload the table
                        // 
                    },
                    error: function (xhr) {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            $.each(errors, function (key, value) {
                                $(`#${key}-error`).html(value[0]);
                            });
                            showAlert('danger', 'Error!', 'Please check the form for errors.');
                        } else {
                            showAlert('danger', 'Error!', 'Error updating organization info.');
                        }
                    },
                    complete: function() {
                        submitBtn.html(originalText).prop('disabled', false);
                    }
                });
            });
            $('#editstreamForm').on('submit', function (e) {
                e.preventDefault();
                const id = $('#editstreamModal #ID').val(); // Fetch the ID value correctly
                const formData = new FormData(this);

                const submitBtn = $(this).find('button[type="submit"]');
                const originalText = submitBtn.html();
                submitBtn.html('<i class="fa fa-spinner fa-spin"></i> Updating...').prop('disabled', true);
                
               
                formData.append('_method', 'POST');
                $.ajax({
                    url: `{{ url('streams') }}/${id}`, // Adjust route as needed
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (response) {
                        showAlert('success', 'Success!', response.message);
                        $('#editstreamModal').modal('hide');
                        loadstreams(); // Reload the table
                        // 
                    },
                    error: function (xhr) {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            $.each(errors, function (key, value) {
                                $(`#${key}-error`).html(value[0]);
                            });
                            showAlert('danger', 'Error!', 'Please check the form for errors.');
                        } else {
                            showAlert('danger', 'Error!', 'Error updating organization info.');
                        }
                    },
                    complete: function() {
                        submitBtn.html(originalText).prop('disabled', false);
                    }
                });
            });
            $('#editclaForm').on('submit', function (e) {
                e.preventDefault();
                const id = $('#editclassModal #ID').val(); // Fetch the ID value correctly
                const formData = new FormData(this);

                const submitBtn = $(this).find('button[type="submit"]');
                const originalText = submitBtn.html();
                submitBtn.html('<i class="fa fa-spinner fa-spin"></i> Updating...').prop('disabled', true);
                
               
                formData.append('_method', 'POST');
                $.ajax({
                    url: `{{ url('classes') }}/${id}`, // Adjust route as needed
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (response) {
                        showAlert('success', 'Success!', response.message);
                        $('#editclassModal').modal('hide');
                        loadclasses(); // Reload the table
                        // 
                    },
                    error: function (xhr) {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            $.each(errors, function (key, value) {
                                $(`#${key}-error`).html(value[0]);
                            });
                            showAlert('danger', 'Error!', 'Please check the form for errors.');
                        } else {
                            showAlert('danger', 'Error!', 'Error updating organization info.');
                        }
                    },
                    complete: function() {
                        submitBtn.html(originalText).prop('disabled', false);
                    }
                });
            });
            

$('#edithouseForm').on('submit', function (e) {  
                e.preventDefault();
                const id = $('#edithouseModal #ID').val(); // Fetch the ID value correctly
                const formData = new FormData(this);

                const submitBtn = $(this).find('button[type="submit"]');
                const originalText = submitBtn.html();
                submitBtn.html('<i class="fa fa-spinner fa-spin"></i> Updating...').prop('disabled', true);
                
               
                formData.append('_method', 'POST');
                $.ajax({
                    url: `{{ url('houses') }}/${id}`, // Adjust route as needed
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (response) {
                        showAlert('success', 'Success!', response.message);
                        $('#edithouseModal').modal('hide');
                        loadhouses(); // Reload the table
                        // 
                    },
                    error: function (xhr) {
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            $.each(errors, function (key, value) {
                                $(`#${key}-error`).html(value[0]);
                            });
                            showAlert('danger', 'Error!', 'Please check the form for errors.');
                        } else {
                            showAlert('danger', 'Error!', 'Error updating organization info.');
                        }
                    },
                    complete: function() {
                        submitBtn.html(originalText).prop('disabled', false);
                    }
                });
            });
            $.ajax({
        url: "{{ route('branches.getDropdown') }}",
        type: "GET",
        success: function (response) {
            const dropdown = $('#branch2');
            const dropdown1 = $('#branch3'); // The dropdown element
            const dropdown2 = $('#branch4');
            dropdown.empty();
            dropdown1.empty(); // Clear existing options
            dropdown2.empty();

            // Add default options
            dropdown.append('<option value="">Select campus</option>');
            dropdown.append('<option value="0">Overall</option>');
            dropdown1.append('<option value="">Select campus</option>');
            dropdown1.append('<option value="0">Overall</option>');
            dropdown2.append('<option value="">Select campus</option>');
            dropdown2.append('<option value="0">Overall</option>');


            // Populate with branches
            response.data.forEach(function (branch) {
                dropdown.append(
                    `<option value="${branch.ID}">${branch.branchname}</option>`
                );
                dropdown1.append(
                    `<option value="${branch.ID}">${branch.branchname}</option>`
                );
                dropdown2.append(
                    `<option value="${branch.ID}">${branch.branchname}</option>`
                );
            });
        },
        error: function () {
            alert('Failed to load branches. Please try again.');
        },
    });

    $.ajax({
        url: "{{ route('streams.getDropdown') }}",
        type: "GET",
        success: function (response) {
           
            const dropdown2 = $('#streamd2');
            
            dropdown.empty();
            dropdown2.empty();
            

            // Add default options
            
            dropdown2.append('<option value="">Select Stream</option>');
           

            // Populate with branches
            response.data.forEach(function (streams) {
                
                dropdown2.append(
                    `<option value="${streams.ID}">${streams.strmname}</option>`
                );
                
            });
        },
        error: function () {
            alert('Failed to load streams. Please try again.');
        },
    });


     $.ajax({
        url: "{{ route('teachers.getHODs') }}",
        type: "GET",
        success: function (response) {
            const dropdown = $('#HOD');
            dropdown.empty();
            dropdown.append('<option value="">Select HOD</option>');
            response.data.forEach(function (teachers) {
                dropdown.append(
                    `<option value="${teachers.workno}">${teachers.fname} ${teachers.surname}</option>`
                );
            });
        },
        error: function () {
            alert('Failed to load streams. Please try again.');
        },
    });

    $('#tab-streams').on('click', function() {
        $.ajax({
            url: "{{ route('classes.getDropdown') }}",
            type: "GET",
            success: function (response) {
                const dropdown = $('#streamclass');
                dropdown.empty();
                dropdown.append('<option value="">Select class</option>');
                response.data.forEach(function (classes) {
                    dropdown.append(
                        `<option value="${classes.ID}">${classes.claname}</option>`
                    );
                });
                dropdown.select2({
                    placeholder: "Select Class",
                    allowClear: true,
                    width: '100%'
                });
            },
            error: function () {
                alert('Failed to load Classes. Please try again.');
            },
        });
        $.ajax({
            url: "{{ route('teachers.getDropdown') }}",
            type: "GET",
            success: function (response) {
                const dropdown = $('#streamteach');
                dropdown.empty();
                dropdown.append('<option value="">Select Teacher</option>');
                response.data.forEach(function (teachers) {
                    dropdown.append(
                        `<option value="${teachers.ID}">${teachers.teachername}</option>`
                    );
                });
                dropdown.select2({
                    placeholder: "Select Teacher",
                    allowClear: true,
                    width: '100%'
                });
            },
            error: function () {
                alert('Failed to load Classes. Please try again.');
            },
        });
      
    });
});
        function validateFile(inputId) {
    const fileInput = document.getElementById(inputId);
    const file = fileInput.files[0];
    const allowedTypes = ['image/png', 'image/jpeg'];
    const maxSize = 2 * 1024 * 1024; // 2 MB

    if (!allowedTypes.includes(file.type)) {
        alert('Only PNG and JPEG files are allowed.');
        fileInput.value = ''; // Reset the input
        return false;
    }

    if (file.size > maxSize) {
        alert('File size should not exceed 2 MB.');
        fileInput.value = ''; // Reset the input
        return false;
    }
    return true;
}


function loadTableData() {
    $.ajax({
        url: "{{ route('staticinfo.getall') }}",
        type: "GET",
        success: function(response) {
            const tableBody = $('#structure-table-body');
            tableBody.empty();
            
            response.data.forEach(function(row) {
                const tr = $('<tr>').attr({
                    
                });
                
                tr.append(`
                    <td hidden>${row.ID}</td>
                    <td>${row.name}</td>
                    <td><img src="${row.logo}" alt="School Logo" style="max-width: 50px; max-height: 50px;"></td>
                    <td>${row.motto}</td>
                    <td hidden>${row.pobox}</td>
                    <td hidden>${row.email}</td>
                    <td hidden>${row.physaddres}</td>
                    <td>
                        <a class="btn btn-link font-24 p-0 line-height-1 no-arrow"
                           href="#"
                           data-toggle="modal"
                           data-target="#editSchoolModal"
                           data-id="${row.ID}"
                           data-name="${row.name}"
                           data-motto="${row.motto}"
                           data-pobox="${row.pobox}"
                           data-email="${row.email}"
                           data-physaddres="${row.physaddres}"
                           data-logo="${row.logo}">
                            <i class="dw dw-edit2"></i>
                        </a>
                    </td>
                `);
                
                tableBody.append(tr);
            });
        },
        error: function(xhr) {
            showAlert('danger', 'Error!', 'Failed to load table data');
        }
    });
}
function loadfcategories(page = 1) {
    $.ajax({
        url: "{{ route('fcategories.getall') }}?page=" + page,
        type: "GET",
        success: function (response) {
            const tableBody = $('#fcategories-table-body');
            const paginationControls = $('#pagination-fcategories');

            tableBody.empty();
            paginationControls.empty();

            // Populate table rows
            response.data.forEach(function (row) {
                const tr = $('<tr>');
                tr.append(`
                    <td>${row.ID}</td>
                    <td>${row.catename}</td>
                    <td>
                        <div class="dropdown">
                            <a class="btn btn-link font-24 p-0 line-height-1 no-arrow dropdown-toggle" href="#" role="button" data-toggle="dropdown">
                                <i class="dw dw-more"></i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right dropdown-menu-icon-list">
                                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#editcampusModal"
                                    data-id="${row.ID}"
                                    data-catename="${row.catename}">
                                    <i class="dw dw-edit2"></i>Edit</a>
                                <a class="dropdown-item" href="#" onclick="confirmDeletion(${row.ID}, '${row.branchname}')">
                                    <i class="dw dw-delete-3"></i> Delete</a>
                            </div>
                        </div>
                    </td>
                `);
                tableBody.append(tr);
            });

            // Handle pagination controls dynamically
            const { current_page, last_page } = response.pagination;

            

            for (let i = 1; i <= last_page; i++) {
                paginationControls.append(`
                    <button class="btn ${i === current_page ? 'btn-primary' : 'btn-light'}" data-page="${i}">${i}</button>
                `);
            }

           
        },
        error: function () {
            showAlert('danger', 'Error!', 'Failed to load table data');
        }
    });
}
function loadpmodes(page = 1) {
    $.ajax({
        url: "{{ route('pmodes.getall') }}?page=" + page,
        type: "GET",
        success: function (response) {
            const tableBody = $('#pmodes-table-body');
            const paginationControls = $('#pagination-pmodes');

            tableBody.empty();
            paginationControls.empty();

            // Populate table rows
            response.data.forEach(function (row) {
                const tr = $('<tr>');
                tr.append(`
                    <td>${row.ID}</td>
                    <td>${row.pname}</td>
                    <td>${row.sstatus}</td>
                    <td>${row.tcode}</td>
                    <td>${row.chequeno}</td>
                    <td>${row.bankn}</td>
                    <td>
                        <div class="dropdown">
                            <a class="btn btn-link font-24 p-0 line-height-1 no-arrow dropdown-toggle" href="#" role="button" data-toggle="dropdown">
                                <i class="dw dw-more"></i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right dropdown-menu-icon-list">
                                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#editpmodeModal"
                                    data-id="${row.ID}"
                                    data-pname="${row.pname}"
                                    data-sstatus="${row.sstatus}"
                                    data-tcode="${row.tcode}"
                                    data-chequeno="${row.chequeno}"
                                    data-bankn="${row.bankn}">
                                    <i class="dw dw-edit2"></i>Edit</a>
                                <a class="dropdown-item" href="#" onclick="confirmDeletion(${row.ID}, '${row.pname}')">
                                    <i class="dw dw-delete-3"></i> Delete</a>
                            </div>
                        </div>
                    </td>
                `);
                tableBody.append(tr);
            });

            // Handle pagination controls dynamically
            const { current_page, last_page } = response.pagination;

            

            for (let i = 1; i <= last_page; i++) {
                paginationControls.append(`
                    <button class="btn ${i === current_page ? 'btn-primary' : 'btn-light'}" data-page="${i}">${i}</button>
                `);
            }

           
        },
        error: function () {
            showAlert('danger', 'Error!', 'Failed to load table data');
        }
    });
}
function loadcampuses(page = 1) {
    $.ajax({
        url: "{{ route('branches.getall') }}?page=" + page,
        type: "GET",
        success: function (response) {
            const tableBody = $('#campuses-table-body');
            const paginationControls = $('#pagination-controls');

            tableBody.empty();
            paginationControls.empty();

            // Populate table rows
            response.data.forEach(function (row) {
                const tr = $('<tr>');
                tr.append(`
                    <td>${row.ID}</td>
                    <td>${row.branchname}</td>
                    <td>
                        <div class="dropdown">
                            <a class="btn btn-link font-24 p-0 line-height-1 no-arrow dropdown-toggle" href="#" role="button" data-toggle="dropdown">
                                <i class="dw dw-more"></i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right dropdown-menu-icon-list">
                                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#editcampusModal"
                                    data-id="${row.ID}"
                                    data-branchname="${row.branchname}">
                                    <i class="dw dw-edit2"></i>Edit</a>
                                <a class="dropdown-item" href="#" onclick="confirmDeletion(${row.ID}, '${row.branchname}')">
                                    <i class="dw dw-delete-3"></i> Delete</a>
                            </div>
                        </div>
                    </td>
                `);
                tableBody.append(tr);
            });

            // Handle pagination controls dynamically
            const { current_page, last_page } = response.pagination;

            

            for (let i = 1; i <= last_page; i++) {
                paginationControls.append(`
                    <button class="btn ${i === current_page ? 'btn-primary' : 'btn-light'}" data-page="${i}">${i}</button>
                `);
            }

           
        },
        error: function () {
            showAlert('danger', 'Error!', 'Failed to load table data');
        }
    });
}
function loadstreams(page = 1) {
    $.ajax({
        url: "{{ route('streams.getall') }}?page=" + page,
        type: "GET",
        success: function (response) {
            const tableBody = $('#stream-table-body');
            const paginationControls = $('#pagination-controls3');

            tableBody.empty();
            paginationControls.empty();

            // Populate table rows
            response.data.forEach(function (row) {
                const tr = $('<tr>');
                tr.append(`
                    <td>${row.ID}</td>
                    <td>${row.strmname}</td>
                    <td>
                        <div class="dropdown">
                            <a class="btn btn-link font-24 p-0 line-height-1 no-arrow dropdown-toggle" href="#" role="button" data-toggle="dropdown">
                                <i class="dw dw-more"></i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right dropdown-menu-icon-list">
                                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#editstreamModal"
                                    data-id="${row.ID}"
                                    data-strmname="${row.strmname}">
                                    <i class="dw dw-edit2"></i>Edit</a>
                                <a class="dropdown-item" href="#" onclick="confirmDeletion(${row.ID}, '${row.strmname}')">
                                    <i class="dw dw-delete-3"></i> Delete</a>
                            </div>
                        </div>
                    </td>
                `);
                tableBody.append(tr);
            });

            // Handle pagination controls dynamically
            const { current_page, last_page } = response.pagination;

            

            for (let i = 1; i <= last_page; i++) {
                paginationControls.append(`
                    <button class="btn ${i === current_page ? 'btn-primary' : 'btn-light'}" data-page="${i}">${i}</button>
                `);
            }
            paginationControls.find('button').on('click', function () {
                const page = $(this).data('page');
                loadstreams(page); // Load houses for the clicked page
            });

           
        },
        error: function () {
            showAlert('danger', 'Error!', 'Failed to load table data');
        }
    });
}
function loadclasses(page = 1) {
    $.ajax({
        url: "{{ route('classes.getall') }}?page=" + page,
        type: "GET",
        success: function (response) {
            const tableBody = $('#classes-table-body');
            const paginationControls = $('#pagination-controls4');

            tableBody.empty();
            paginationControls.empty();

            // Populate table rows
            response.data.forEach(function (row) {
                const tr = $('<tr>');
                tr.append(`
                    <td>${row.ID}</td>
                    <td>${row.strmname}</td>
                    <hidden td>${row.stid}</td>
                    <td>${row.claname}</td>
                    <td>${row.clarank}</td>
                    <td>${row.clateach}</td>
                    <td>
                        <div class="dropdown">
                            <a class="btn btn-link font-24 p-0 line-height-1 no-arrow dropdown-toggle" href="#" role="button" data-toggle="dropdown">
                                <i class="dw dw-more"></i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right dropdown-menu-icon-list">
                                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#editclassModal"
                                    data-id="${row.ID}"
                                    data-stid="${row.stid}"
                                    data-claname="${row.claname}"
                                    data-clarank="${row.clarank}"
                                    data-clateach="${row.clateach}">
                                    <i class="dw dw-edit2"></i>Edit</a>
                                <a class="dropdown-item" href="#" onclick="confirmDeletion(${row.ID}, '${row.strmname}')">
                                    <i class="dw dw-delete-3"></i> Delete</a>
                            </div>
                        </div>
                    </td>
                `);
                tableBody.append(tr);
            });

            // Handle pagination controls dynamically
            const { current_page, last_page } = response.pagination;

            

            for (let i = 1; i <= last_page; i++) {
                paginationControls.append(`
                    <button class="btn ${i === current_page ? 'btn-primary' : 'btn-light'}" data-page="${i}">${i}</button>
                `);
            }
            paginationControls.find('button').on('click', function () {
                const page = $(this).data('page');
                loadstreams(page); // Load houses for the clicked page
            });

           
        },
        error: function () {
            showAlert('danger', 'Error!', 'Failed to load table data');
        }
    });
}
function loadperiods(page = 1) {
    $.ajax({
        url: "{{ route('periods.getall') }}?page=" + page,
        type: "GET",
        success: function (response) {
            const tableBody = $('#periods-table-body');
            const paginationControls = $('#pagination-controlsperiod');

            tableBody.empty();
            paginationControls.empty();

            

            // Populate table rows
            response.data.forEach(function (row) {
                const pstatusColor = row.pstatus === 'Active' ? 'green' : 'red';

                const tr = $('<tr>');
                tr.append(`
                    <td>${row.ID}</td>
                    <td>${row.periodname}</td>
                    <td><span style="color: ${pstatusColor}; font-weight: bold;">${row.pstatus}</span></td>
                    <td>${row.startdate_formatted}</td>
                    <td>${row.enddate_formatted}</td>

                    <td>
                        <div class="dropdown">
                            <a class="btn btn-link font-24 p-0 line-height-1 no-arrow dropdown-toggle" href="#" role="button" data-toggle="dropdown">
                                <i class="dw dw-more"></i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right dropdown-menu-icon-list">
                                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#editperiodsModal"
                                    data-id="${row.ID}"
                                    data-periodname="${row.periodname}"
                                    data-startdate="${row.startdate}"
                                    data-enddate="${row.enddate}"
                                    data-pstatus="${row.pstatus}">
                                    <i class="dw dw-edit2"></i>Edit</a>
                                <a class="dropdown-item" href="#" onclick="confirmDeletion(${row.ID}, '${row.branchname}')">
                                    <i class="dw dw-delete-3"></i> Delete</a>
                            </div>
                        </div>
                    </td>
                `);
                tableBody.append(tr);
            });

            // Handle pagination controls dynamically
            const { current_page, last_page } = response.pagination;

            for (let i = 1; i <= last_page; i++) {
                paginationControls.append(`
                    <button class="btn ${i === current_page ? 'btn-primary' : 'btn-light'}" data-page="${i}">${i}</button>
                `);
            }

            // Add click event for pagination buttons
            paginationControls.find('button').on('click', function () {
                const page = $(this).data('page');
                loadperiods(page);
            });
        },
        error: function () {
            showAlert('danger', 'Error!', 'Failed to load table data');
        }
    });
}

function loadhouses(page = 1) {
    $.ajax({
        url: "{{ route('houses.getall') }}?page=" + page,
        type: "GET",
        success: function (response) {
            const tableBody = $('#houses-table-body');
            const paginationControls = $('#pagination-controls2');

            tableBody.empty();
            paginationControls.empty();

            // Populate table rows
            response.data.forEach(function (row) {
                const tr = $('<tr>');
                tr.append(`
                    <td>${row.ID}</td>
                    <td hidden>${row.brid}</td>
                    <td>${row.branchname}</td> <!-- Display branchname instead of brid -->
                    <td>${row.housen}</td>
                    <td>
                        <div class="dropdown">
                            <a class="btn btn-link font-24 p-0 line-height-1 no-arrow dropdown-toggle" href="#" role="button" data-toggle="dropdown">
                                <i class="dw dw-more"></i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right dropdown-menu-icon-list">
                                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#edithouseModal"
                                    data-id="${row.ID}"
                                    data-brid="${row.brid}"
                                    data-housen="${row.housen}"> <!-- Include branchname -->
                                    <i class="dw dw-edit2"></i>Edit</a>
                                <a class="dropdown-item" href="#" onclick="confirmDeletion(${row.ID}, '${row.branchname}')">
                                    <i class="dw dw-delete-3"></i> Delete</a>
                            </div>
                        </div>
                    </td>
                `);
                tableBody.append(tr);
            });

            // Handle pagination controls dynamically
            const { current_page, last_page } = response.pagination;

            for (let i = 1; i <= last_page; i++) {
                paginationControls.append(`
                    <button class="btn ${i === current_page ? 'btn-primary' : 'btn-light'}" data-page="${i}">${i}</button>
                `);
            }

            // Add click event for pagination buttons
            paginationControls.find('button').on('click', function () {
                const page = $(this).data('page');
                loadhouses(page); // Load houses for the clicked page
            });
        },
        error: function () {
            showAlert('danger', 'Error!', 'Failed to load table data');
        }
    });
}

// Event delegation for dynamic pagination controls
$(document).on('click', '#pagination-controls button', function () {
    const page = $(this).data('page');
    loadcampuses(page);
});

function confirmDeletion(ID, branchname) {
    swal({
        title: 'Are you sure?',
        text: `Are you sure you want to delete: "${branchname}"?`,
        type: 'warning',
        showCancelButton: true,
        confirmButtonClass: 'btn btn-success',
        cancelButtonClass: 'btn btn-danger',
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'No, cancel!'
    }).then((result) => {
        // Only proceed if the user clicked confirm
        if (result.value === true) {  // Check specifically for true
            // Perform the AJAX request for deletion
            $.ajax({
                url: `{{ url('branches') }}/${ID}`,
                type: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        swal({
                            title: 'Deleted!',
                            text: 'Branch deleted successfully!',
                            icon: 'success',
                            buttons: false,
                            timer: 2000
                        });
                        loadcampuses();
                    } else {
                        swal({
                            title: 'Error!',
                            text: response.message || 'Failed to delete.',
                            icon: 'error',
                            buttons: true
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX error:', error);
                    swal({
                        title: 'Failed!',
                        text: 'An error occurred while deleting the branch. Please try again.',
                        icon: 'error',
                        buttons: true
                    });
                }
            });
        }
    });
}


    </script>
</x-custom-admin-layout>
