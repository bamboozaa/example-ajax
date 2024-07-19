@extends('layouts.app')

@section('importjs')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.min.js"
        integrity="sha384-cuYeSxntonz0PPNlHhBs68uyIAVpIIOZZ5JqeqvYYIcEL727kskC66kF92t6Xl2V" crossorigin="anonymous">
    </script>

    @parent
    <script type="module">
        // @if (session('success'))
        //     Swal.fire({
        //         title: 'Success!',
        //         text: '{{ session('success') }}',
        //         icon: 'success'
        //     });
        // @endif

        $(function() {

            /*------------------------------------------
             --------------------------------------------
             Pass Header Token
             --------------------------------------------
             --------------------------------------------*/
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            /*------------------------------------------
            --------------------------------------------
            Render DataTable
            --------------------------------------------
            --------------------------------------------*/
            var table = $('.data-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('products-ajax-crud.index') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex'
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'description',
                        name: 'description'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                ]
            });

            var myModal = new bootstrap.Modal(document.getElementById('ajaxModel'));

            /*------------------------------------------
            --------------------------------------------
            Click to Button
            --------------------------------------------
            --------------------------------------------*/
            $('#createNewProduct').click(function() {
                $('#saveBtn').val("create-product");
                $('#product_id').val('');
                $('#productForm').trigger("reset");
                $('#modelHeading').html("Create New Product");
                // $('#ajaxModel').modal('show');
                myModal.show();
            });

            /*------------------------------------------
            --------------------------------------------
            Click to Edit Button
            --------------------------------------------
            --------------------------------------------*/
            $('body').on('click', '.editProduct', function() {
                var product_id = $(this).data('id');
                $.get("{{ route('products-ajax-crud.index') }}" + '/' + product_id + '/edit', function(
                    data) {
                    $('#modelHeading').html("Edit Product");
                    $('#saveBtn').val("edit-user");
                    // $('#ajaxModel').modal('show');
                    myModal.show();
                    $('#product_id').val(data.id);
                    $('#name').val(data.name);
                    $('#description').val(data.description);
                })
            });

            /*------------------------------------------
            --------------------------------------------
            Create Product Code
            --------------------------------------------
            --------------------------------------------*/
            $('#saveBtn').click(function(e) {
                e.preventDefault();
                // $(this).html('Sending..');

                $.ajax({
                    data: $('#productForm').serialize(),
                    url: "{{ route('products-ajax-crud.store') }}",
                    type: "POST",
                    dataType: 'json',
                    success: function(data) {
                        Swal.fire({
                            title: 'Success!',
                            text: data.success,
                            icon: 'success'
                        });
                        $('#productForm').trigger("reset");
                        // $('#ajaxModel').modal('hide');
                        myModal.hide();
                        table.draw();
                        // console.log('Success:', data);
                    },
                    error: function(data) {
                        console.log('Error:', data);
                        $('#saveBtn').html('Save Changes');
                    }
                });
            });

            /*------------------------------------------
            --------------------------------------------
            Delete Product Code
            --------------------------------------------
            --------------------------------------------*/
            $('body').on('click', '.deleteProduct', function() {

                var product_id = $(this).data("id");
                confirm("Are You sure want to delete !");

                $.ajax({
                    type: "DELETE",
                    url: "{{ route('products-ajax-crud.store') }}" + '/' + product_id,
                    success: function(data) {
                        table.draw();
                    },
                    error: function(data) {
                        console.log('Error:', data);
                    }
                });
            });

        });
    </script>

@stop

@section('content')

    <div class="container">
        <div class="header">
            <h1>Laravel Ajax CRUD</h1>
            <a class="btn btn-success" href="javascript:void(0)" id="createNewProduct"> Create New Product</a>
            <table class="table table-bordered data-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Name</th>
                        <th>Details</th>
                        <th width="280px">Action</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>

    <div class="modal fade" id="ajaxModel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="modelHeading"></h4>
                </div>
                <div class="modal-body">
                    <form id="productForm" name="productForm" class="form-horizontal">
                        <input type="hidden" name="product_id" id="product_id">
                        <div class="form-group">
                            <label for="name" class="col-sm-2 control-label">Name</label>
                            <div class="col-sm-12">
                                <input type="text" class="form-control" id="name" name="name"
                                    placeholder="Enter Name" value="" maxlength="50" required="">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">Description</label>
                            <div class="col-sm-12">
                                <textarea id="description" name="description" required="" placeholder="Enter Descriptions" rows="5"
                                    cols="5" class="form-control"></textarea>
                            </div>
                        </div>

                        <div class="col-sm-offset-2 col-sm-10">
                            <button type="submit" class="btn btn-primary" id="saveBtn" value="create">Save changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection
