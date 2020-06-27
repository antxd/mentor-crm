jQuery(document).ready(function($){
    var tomorrow = new Date();
    tomorrow.setDate(tomorrow.getDate() + 1);
    $('.crmdate input').datepicker({
        language: 'es',
        minDate: tomorrow,
        onSelect: function(date) {
            //alert(date);
            $('.crmdate input').datepicker().data('datepicker').hide()
        }
    })
    $('input[name="tag-consultatipo"]').click(function(){
        if ($(this).val() == 1) {
            $('.mentor-crm-images-tag').slideDown()
        }else{
            $('.mentor-crm-images-tag').slideUp()
        }
    })
    $('.mentor-crm-modal-close').click(function(){
        $('.mentor-crm-modal-response').fadeOut()
    })
    $('#foto1').on('change',function(){
        var fullPath = document.getElementById('foto1').value;
        if (fullPath) {
            var startIndex = (fullPath.indexOf('\\') >= 0 ? fullPath.lastIndexOf('\\') : fullPath.lastIndexOf('/'));
            var filename = fullPath.substring(startIndex);
            if (filename.indexOf('\\') === 0 || filename.indexOf('/') === 0) {
                filename = filename.substring(1);
            }
            $('#foto1_name').hide().text(filename).fadeIn()
        }
    })
    $('#foto2').on('change',function(){
        var fullPath = document.getElementById('foto2').value;
        if (fullPath) {
            var startIndex = (fullPath.indexOf('\\') >= 0 ? fullPath.lastIndexOf('\\') : fullPath.lastIndexOf('/'));
            var filename = fullPath.substring(startIndex);
            if (filename.indexOf('\\') === 0 || filename.indexOf('/') === 0) {
                filename = filename.substring(1);
            }
            $('#foto2_name').hide().text(filename).fadeIn()
        }
    })
    $('.select_file').click(function(){
        $('#'+$(this).data('inputid')).trigger('click')
        
    })

    var percent = $('#dynamic_percent_mentor');
       
    $('.mentor-lead-capture-form-ajax').ajaxForm({
        url: mentor_ajax_url,
        //data: data,
        //cache: false,
        //contentType: false,
        //processData: false,
        //method: 'POST',
        type: 'POST',
        beforeSend: function() {
            $('body').css('overflow','hidden');
            $('.mentor-crm-modal-response_loader').show()
            var percentVal = '0%';
            percent.html(percentVal);
        },
        uploadProgress: function(event, position, total, percentComplete) {
            var percentVal = percentComplete + '%';
            percent.html(percentVal);
        },
        success: function(data) {
            console.log(data)
            var percentVal = '100%';
            percent.html(percentVal);
            if (data.msg == 'ok') {
                needToConfirm = false;
                if (data.msg_fail) {
                    alert(data.msg_fail);
                }
                $.post(mentor_ajax_url,{action:'mentor_lead_capture_2fire',lid:data.lid})
                $('.mentor-crm-modal-response_loader').hide()
                $('.mentor-crm-modal-response').fadeIn()
                setTimeout("location.href = '"+data.payment_url+"'",3000)
            }    
        },
        complete: function(xhr) {
            console.log('finished')
            //status.html(xhr.responseText);
        }
    }); 

    /*$('.mentor-lead-capture-form-ajax').submit(function(){
        var $this = $(this)
        var data = new FormData($this[0])
        $this.addClass('form-ajax-sending')
        $.ajax({
            url: "<?php echo admin_url('admin-ajax.php'); ?>",
            data: data,
            cache: false,
            contentType: false,
            processData: false,
            method: 'POST',
            type: 'POST', // For jQuery < 1.9
            success: function(data){
                if (data.msg == 'ok') {
                    $this.trigger('reset')
                    $('.mentor-crm-modal-response').fadeIn();
                    $('#foto1_name,#foto2_name').hide().text('')
                    setTimeout("location.href = '"+data.payment_url+"'",3000)
                }else{
                    alert('Error, intenta de nuevo.')
                }
                $this.removeClass('form-ajax-sending')
                //console.log(data)        
            }
        });
        return false;
    })
    */
})
