@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <div class="panel panel-default">
                    <div class="panel-heading">Generate Data File</div>
                    <div class="panel-body">
                        <form>
                            <div class="row">
                                <div class="col-md-6">
                                    {{ csrf_field() }}
                                    <div class="form-group">
                                        <label for="output">Output format: </label><br>
                                        <input type="checkbox" name="output[]" id="json" value="json"/> <label
                                                for="json">JSON</label><br>
                                        <input type="checkbox" name="output[]" id="xml" value="xml"/> <label for="xml">XML</label><br>
                                        <input type="checkbox" name="output[]" id="sqlite" value="sqlite"/> <label
                                                for="sqlite">SqLite</label><br>
                                        <input type="checkbox" name="output[]" id="yaml" value="yaml"/> <label
                                                for="yaml">YAML</label><br>
                                        <input type="checkbox" name="output[]" id="html" value="html"/> <label
                                                for="html">HTML</label>
                                    </div>

                                    <div class="form-group">
                                        <label>File Versioning: </label>
                                        <input type="radio" name="versioning" id="versioning_no" value="0" checked/>
                                        <label for="versioning_no">No</label>
                                        <input type="radio" name="versioning" id="versioning_yes" value="1"/> <label
                                                for="versioning_yes">Yes</label>
                                    </div>

                                    <div class="form-group">
                                        <label>URL validation: </label><br>
                                        <input type="radio" name="dns_validation" id="pattern_validation" value="0"
                                               checked/>
                                        <label for="pattern_validation">Just URL pattern validation</label><br>
                                        <input type="radio" name="dns_validation" id="dns_validation" value="1"/>
                                        <label for="dns_validation">DNS validation <i>(time consuming)</i></label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Sort Data: </label>
                                        <select name="sort" class="form-control">
                                            <option value="">Select Sort By</option>
                                            <option value="name">Name</option>
                                            <option value="address">Address</option>
                                            <option value="stars">Stars</option>
                                            <option value="contact">Contact</option>
                                            <option value="phone">Phone</option>
                                            <option value="uri">Uri</option>
                                        </select>
                                        <select name="sort_order" class="form-control">
                                            <option value="">Select Sort Order</option>
                                            <option value="asc">Ascending</option>
                                            <option value="desc">Descending</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Group Data: </label>
                                        <select name="group" class="form-control">
                                            <option value="">Select Group By</option>
                                            <option value="name">Name</option>
                                            <option value="address">Address</option>
                                            <option value="stars">Stars</option>
                                            <option value="contact">Contact</option>
                                            <option value="phone">Phone</option>
                                            <option value="uri">Uri</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Filter Data: </label>
                                        <select name="filter" class="form-control">
                                            <option value="">Select Filter By</option>
                                            <option value="name">Name</option>
                                            <option value="address">Address</option>
                                            <option value="stars">Stars</option>
                                            <option value="contact">Contact</option>
                                            <option value="phone">Phone</option>
                                            <option value="uri">Uri</option>
                                        </select>
                                        <input type="text" class="form-control" name="filter_value"
                                               placeholder="Filter value">
                                    </div>
                                </div>
                            </div>

                            <button id="submit-button" type="submit" data-loading-text="Processing..."
                                    class="btn btn-primary">Generate
                            </button>
                        </form>
                        <br><br>
                        <div id="responses">

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


@section('after_scripts')

    <script>

        $(document).ready(function () {
            $('form').submit(function (event) {
                $('#submit-button').button('loading');
                $('#responses').html('');
                var formData = $(this).serialize();

                $.ajax({
                    url: '/generate-file',
                    type: 'post',
                    data: formData,
                    success: function (data, textStatus, jQxhr) {
                        $('#submit-button').button('reset');
                        var html = '<div class="alert alert-success">';
                        html += data.message;
                        html += '</div>';
                        $('#responses').html(html);
                    },
                    error: function (jqXhr, textStatus, errorThrown) {
                        $('#submit-button').button('reset');
                        var errorsHtml = '<div class="alert alert-danger"><p>Error occurred:</p><ul>';

                        if (jqXhr.status === 422) {
                            //process validation errors here.
                            $errors = jqXhr.responseJSON;
                            //errorsHtml += '<li>' + $errors.message + '</li>';

                            $.each($errors.errors, function (key, value) {
                                errorsHtml += '<li>' + value[0] + '</li>';
                            });
                        } else {
                            errorsHtml += '<li>' + errorThrown + '</li>';
                        }
                        errorsHtml += '</ul></di>';

                        $('#responses').html(errorsHtml);
                    }
                });
                event.preventDefault();
            });

        });

    </script>
@endsection
