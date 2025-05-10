@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Driver Application Details</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.driver-applications.index') }}" class="btn btn-default">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h4>Personal Information</h4>
                            <table class="table table-bordered">
                                <tr>
                                    <th style="width: 200px;">Full Name</th>
                                    <td>{{ $application->full_name }}</td>
                                </tr>
                                <tr>
                                    <th>Email</th>
                                    <td>{{ $application->email }}</td>
                                </tr>
                                <tr>
                                    <th>Phone Number</th>
                                    <td>{{ $application->phone_number }}</td>
                                </tr>
                                <tr>
                                    <th>Date of Birth</th>
                                    <td>{{ $application->date_of_birth }}</td>
                                </tr>
                                <tr>
                                    <th>Gender</th>
                                    <td>{{ ucfirst($application->gender) }}</td>
                                </tr>
                                <tr>
                                    <th>ID Card Number</th>
                                    <td>{{ $application->id_card_number }}</td>
                                </tr>
                                <tr>
                                    <th>ID Card Issue Date</th>
                                    <td>{{ $application->id_card_issue_date }}</td>
                                </tr>
                                <tr>
                                    <th>ID Card Issue Place</th>
                                    <td>{{ $application->id_card_issue_place }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h4>Address Information</h4>
                            <table class="table table-bordered">
                                <tr>
                                    <th style="width: 200px;">Address</th>
                                    <td>{{ $application->address }}</td>
                                </tr>
                                <tr>
                                    <th>City</th>
                                    <td>{{ $application->city }}</td>
                                </tr>
                                <tr>
                                    <th>District</th>
                                    <td>{{ $application->district }}</td>
                                </tr>
                            </table>

                            <h4>Vehicle Information</h4>
                            <table class="table table-bordered">
                                <tr>
                                    <th style="width: 200px;">Vehicle Type</th>
                                    <td>{{ ucfirst($application->vehicle_type) }}</td>
                                </tr>
                                <tr>
                                    <th>Vehicle Model</th>
                                    <td>{{ $application->vehicle_model }}</td>
                                </tr>
                                <tr>
                                    <th>Vehicle Color</th>
                                    <td>{{ $application->vehicle_color }}</td>
                                </tr>
                                <tr>
                                    <th>License Plate</th>
                                    <td>{{ $application->license_plate }}</td>
                                </tr>
                                <tr>
                                    <th>Driver License Number</th>
                                    <td>{{ $application->driver_license_number }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-6">
                            <h4>Bank Information</h4>
                            <table class="table table-bordered">
                                <tr>
                                    <th style="width: 200px;">Bank Name</th>
                                    <td>{{ $application->bank_name }}</td>
                                </tr>
                                <tr>
                                    <th>Account Number</th>
                                    <td>{{ $application->bank_account_number }}</td>
                                </tr>
                                <tr>
                                    <th>Account Name</th>
                                    <td>{{ $application->bank_account_name }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h4>Emergency Contact</h4>
                            <table class="table table-bordered">
                                <tr>
                                    <th style="width: 200px;">Contact Name</th>
                                    <td>{{ $application->emergency_contact_name }}</td>
                                </tr>
                                <tr>
                                    <th>Contact Phone</th>
                                    <td>{{ $application->emergency_contact_phone }}</td>
                                </tr>
                                <tr>
                                    <th>Relationship</th>
                                    <td>{{ ucfirst($application->emergency_contact_relationship) }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-12">
                            <h4>Documents</h4>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="card">
                                        <div class="card-header">
                                            <h5 class="card-title">ID Card Front</h5>
                                        </div>
                                        <div class="card-body">
                                            <img src="{{ asset($application->id_card_front_image) }}" class="img-fluid" alt="ID Card Front">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card">
                                        <div class="card-header">
                                            <h5 class="card-title">ID Card Back</h5>
                                        </div>
                                        <div class="card-body">
                                            <img src="{{ asset($application->id_card_back_image) }}" class="img-fluid" alt="ID Card Back">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card">
                                        <div class="card-header">
                                            <h5 class="card-title">Driver License</h5>
                                        </div>
                                        <div class="card-body">
                                            <img src="{{ asset($application->driver_license_image) }}" class="img-fluid" alt="Driver License">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card">
                                        <div class="card-header">
                                            <h5 class="card-title">Vehicle Registration</h5>
                                        </div>
                                        <div class="card-body">
                                            <img src="{{ asset($application->vehicle_registration_image) }}" class="img-fluid" alt="Vehicle Registration">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($application->status === 'pending')
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4>Actions</h4>
                                </div>
                                <div class="card-body">
                                    <form action="{{ route('admin.driver-applications.approve', $application) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-success" onclick="return confirm('Are you sure you want to approve this application?')">
                                            <i class="fas fa-check"></i> Approve Application
                                        </button>
                                    </form>
                                    <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#rejectModal">
                                        <i class="fas fa-times"></i> Reject Application
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Reject Modal -->
                    <div class="modal fade" id="rejectModal" tabindex="-1" role="dialog" aria-labelledby="rejectModalLabel" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <form action="{{ route('admin.driver-applications.reject', $application) }}" method="POST">
                                    @csrf
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="rejectModalLabel">Reject Application</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="form-group">
                                            <label for="admin_notes">Reason for Rejection</label>
                                            <textarea name="admin_notes" id="admin_notes" class="form-control" rows="3" required></textarea>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-danger">Reject Application</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if($application->status === 'rejected' && $application->admin_notes)
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4>Rejection Reason</h4>
                                </div>
                                <div class="card-body">
                                    <p>{{ $application->admin_notes }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 