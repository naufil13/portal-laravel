{{-- Modal --}}
<div class="modal fade" id="directories-modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Directories List</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body kt-padding-0"></div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        $('a[action="directories"]').bind('click', function (e) {
            e.preventDefault();
            let _this = $(this);
            $.ajax(_this.attr('href'), {
                type: 'GET',
                data: {},
                dataType: 'JSON',
                success: function (data, status, xhr) {
                    //$('#directories-modal .modal-title').html(_this.closest('tr').find('.gtd-name').text() + ' - Directory');
                    $('#directories-modal .modal-title').html(data.member.company + ' - Directory');
                    $('#directories-modal .modal-body').html(data.html);
                    $('#directories-modal').modal('show');
                },
                error: function (jqXhr, textStatus, errorMessage) {
                    $.notify('<strong>Error</strong> ' + errorMessage, {type: 'danger'});
                }
            });


            return false;
        })
    });
</script>
