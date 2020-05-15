<?php
// [bartag foo="foo-value"]
function mentor_lead_capture_form( $atts ) {
	global $crmcountries;
	$a = shortcode_atts( array(
	//	'foo' => ''
	), $atts );
	//return "foo = {$a['foo']}";
	ob_start();
    $terms_url = get_option('mentor_crm_terms_url');
	?>
<style type="text/css">
.mentor-lead-capture-form *,.crm-row * {
    box-sizing: border-box;
}
.mentor-lead-capture-form{
	color: #4e4e4e;
    transition: all .5s ease-in-out;
}
.form-ajax-sending {
    opacity: .5;
    pointer-events: none;
}
.crm-text-center{text-align: center;}
.crm-text-right{text-align: right;}
.crm-row:before,
.crm-row:after{
  content: " ";
  display: table;
  clear: both;
}
[class*="crm-col"]{
  width: 100%;
  padding: 0 15px;
  float: left;
  position: relative;
  box-sizing: border-box;
}
.crm-col-30{
  width: 30%;
}
.crm-col-50{
  width: 50%;
}
.crm-col-70{
  width: 70%;
}
@media(max-width: 992px){
    .crm-col-30,.crm-col-50,.crm-col-70{ width:100%; }
}
.mentor-lead-box {
	background-color: rgba(255,255,255,.4);
    border: 1px solid #6c6c6c;
    border-radius: 15px;
    margin-bottom: 30px;
}
.mentor-lead-box h3 {
    text-align: center;
    margin: 0;
    padding: 20px;
    font-size: 16px;
    color: #4e4e4e;
}
.form-wrap {
    margin-bottom: 30px !important;
}
.mentor-lead-capture-form .form-wrap input,
.mentor-lead-capture-form .form-wrap select {
    width: 100%;
    max-width: 100%;
    border: 1px solid #6c6c6c;
    color: #4e4e4e;
    line-height: 1;
    height: 44px;
    padding: 0 15px;
    transition: all .3s ease-in-out;
    -webkit-appearance: none;
    border-radius: 0px;
    outline: none;
    background: #fff;
}
.mentor-lead-capture-form .form-wrap textarea {
    width: 100%;
    min-height: 100px;
    border: 1px solid #4e4e4e;
    color: #4e4e4e;
    outline: none;
    transition: all .3s ease-in-out;
    padding: 8px;
}
.mentor-lead-capture-form .form-wrap input:focus,
.mentor-lead-capture-form .form-wrap select:focus,
.mentor-lead-capture-form .form-wrap textarea:focus {
    border-color: #4e4e4e;
    color: #4e4e4e;
    box-shadow: 0 0 2px 0px #4e4e4e;
}
.mentor-lead-capture-form .form-wrap button {
    cursor: pointer;
    -webkit-appearance: none;
    border: 1px solid #ff0000;
    background: #ff0000;
    color: #fff;
    padding: 10px 25px;
    border-radius: 8px;
    transition: all .3s ease-in-out;
    outline: none;
    margin: .5em 0;
}
.mentor-lead-capture-form .form-wrap button:hover,
.mentor-lead-capture-form .form-wrap button:focus {
    color: #ff0000;
    background: #fff;
    box-shadow: 0 0 0 1px #ff0000;
    text-decoration: none;
}
.mentor-lead-box input[type="radio"],
.mentor-lead-box input[type="checkbox"] {
    display: inline-block;
    width: 20px;
    height: 20px;
    margin: 0 5px;
    line-height: 1;
    position: relative;
    top: 3px;
    border-radius: 5px;
    -webkit-appearance: none;
    background: #fff;
    padding: 0;
    border: 1px solid #6c6c6c;
    outline: none;
    cursor: pointer;
}
.mentor-lead-box input[type="radio"]:checked:before,
.mentor-lead-box input[type="checkbox"]:checked:before {
    content: "";
    display: inline-block;
    width: 8px;
    height: 8px;
    background-color: #6c6c6c;
    position: absolute;
    top: 5px;
    left: 5px;
    border-radius: 50%;
    margin: 0;
}
.mentor-lead-box label {
    display: inline-block;
    font-weight: normal;
}
.form-wrap.inline-input input{
	display: inline-block;
	width: initial;
	max-width: initial;
}
.mentor-crm-images-tag input[type="file"] {
    display: none;
    border: 0px;
    box-shadow: none !important;
}
.mentor-crm-images-tag img {
    width: 100px;
    display: block;
    margin: 0 auto 10px;
}
.title-images {
    display: block;
    font-size: 1.3em;
    font-weight: bold;
}
.sub-title-images {
    display: block;
    font-size: .8em;
    margin-bottom: 10px;
}
.mentor-lead-box.mentor-crm-images-tag label {
    cursor: pointer;
    width: 45%;
    position: relative;
}
.form-wrap p {
    margin: 0;
    line-height: 1.1;
    padding: 5px 0;
}
.required-text {
    margin: 0;
    color: #ff0000;
    padding: 5px 0;
    line-height: 1.2;
}
.mentor-submit-area label{
	cursor: pointer;
}
.mentor-crm-modal-response {
    display: none;
    position: fixed;
    top: 0;
    right: 0;
    width: 100%;
    height: 100vh;
    z-index: 2000;
    background: rgba(255, 255, 255, .8);
}
.mentor-crm-modal-body {
    position: relative;
    margin: 200px auto;
    display: block;
    max-width: 35%;
    background: #fff;
    border: 10px solid #eeeeee;
    padding: 15px 15px 80px;
    text-align: center;
    border-radius: 5px;
}
.mentor-crm-modal-close {
    background: #ed3833;
    position: absolute;
    top: -45px;
    right: -45px;
    -webkit-appearance: none;
    border: none;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    cursor: pointer;
    color: #fff;
    font-size: 20px;
    line-height: 25px;
}
.mentor-crm-modal-body h1 {
    font-weight: bold;
    font-size: 26px;
    margin: 15px 0;
    font-style: italic;
    color: #555;
}
.mentor-crm-modal-body h2 {
    font-weight: normal;
    font-size: 18px;
    margin: 15px 0;
    font-style: italic;
    color: #555;
}
.mentor-lead-box.mentor-submit-area {
    background: transparent;
    border: none;
}
.mentor-lead-capture-form .form-wrap button.mentor-crm-submit {
    margin: 1.5em 0 0;
}
.crm-col-50.form-wrap.crmcountries:after {
    content: "〈";
    display: block;
    position: absolute;
    transform: rotate(-90deg);
    top: 18px;
    font-size: 20px;
    right: 30px;
}
#foto1_name, #foto2_name {
    font-size: 80%;
    position: absolute;
    bottom: -20px;
    left: 10%;
    width: 80%;
    text-overflow: ellipsis;
    display: block;
    white-space: nowrap;
    overflow: hidden;
}
</style>
<div class="mentor-lead-capture-form">
	<form class="mentor-lead-capture-form-ajax">
		<input type="hidden" name="action" value="mentor_lead_capture">
         <?php wp_nonce_field( 'mentor-form-lead-capture','mentor-crm-front-nonce'); ?>
	<div class="mentor-lead-box">
		<div class="crm-row">
			<div class="crm-col">
				<h3>INFORMACIÓN PERSONAL</h3>
			</div>
			<div class="crm-col-50 form-wrap">
                <input type="text" name="firstname" placeholder="NOMBRE &#42;" required="true">
            </div>
            <div class="crm-col-50 form-wrap">
                <input type="text" name="lastname" placeholder="APELLIDO &#42;" required="true">
            </div>
            <div class="crm-col-50 form-wrap">
                <input type="text" name="email" placeholder="E-MAIL &#42;" required="true">
            </div>
            <div class="crm-col-50 form-wrap">
                <input type="text" name="phone" placeholder="TÉLEFONO / MÓVIL &#42;"  required="true">
            </div>
            <div class="crm-col-50 form-wrap crmcountries">
                <select name="country" required="true">
                	<option value="">PAÍS &#42;</option>
                	<?php
                        $first_coutries = $second_coutries = '';
                		foreach ($crmcountries as $key => $country) {
                            if ($key == 'CO' || $key == 'AR' || $key == 'EC' || $key == 'CL' || $key == 'BR' || $key == 'PE' || $key == 'UY' || $key == 'PY' || $key == 'PA') {
                                $first_coutries .= "<option value='{$key}'>{$country}</option>";
                            }else{
                                $second_coutries .= "<option value='{$key}'>{$country}</option>";
                            }
                			
                		}
                        echo $first_coutries.'<optgroup label="Otro">'.$second_coutries.'</optgroup>';
                	?>
                </select>
            </div>
            <div class="crm-col-50 form-wrap">
                <input type="text" name="city" placeholder="CIUDAD &#42;" required="true">
            </div>
            
		</div>
	</div>
	<div class="mentor-lead-box">
		<div class="crm-row">
			<div class="crm-col">
				<h3>CONSULTA &#42;</h3>
			</div>
			<div class="crm-col form-wrap crm-text-center tag-consultatipo-wrap">
				<label>
					<input type="radio" name="tag-consultatipo" value="1" required="true" checked> TELE ORIENTACIÓN MÉDICA
				</label>
               	<label>
					<input type="radio" name="tag-consultatipo" value="2"> PRESENCIAL
				</label>
				<label>
					<input type="radio" name="tag-consultatipo" value="3"> PREANESTESIA
				</label>
				
				<div class="crm-col form-wrap inline-input crm-text-center crmdate">
                <label style="padding-right: 5px;">FECHA</label>
                <input type="date" name="date" required="true" min="<?php echo date('Y-m-d',strtotime("+1 days")); ?>">
            </div>
            </div>
		</div>
	</div>
	<div class="mentor-lead-box">
		<div class="crm-row">
			<div class="crm-col">
				<h3>CIRUGÍA DE INTERÉS &#42;</h3>
			</div>
            <div class="crm-col form-wrap">
                <input type="text" name="reason" placeholder="Escríbe aquí &#42;"  required="true">
            </div>
		</div>
	</div>
	<div class="mentor-lead-box mentor-crm-images-tag">
		<div class="crm-row">
			<div class="crm-col">
				<h3>SUBE TUS FOTOS (opcional)</h3>
			</div>
            <div class="crm-col form-wrap inline-input crm-text-center">
            	<label class="">
            		<img src="<?php echo plugins_url('/'.MENTOR_CRM_FOLDER.'/assets/dhara-perfil.svg'); ?>">
            		<span class="title-images">PERFIL</span>
                    <button type="button" class="select_file" data-inputid="foto1">SUBIR FOTO</button>
            		<input type="file" name="lead_image[]" accept="image/*" class="check-required ocultar_archivo" id="foto1">
                    <span id="foto1_name"></span>
            	</label>
            	<label>
            		<img src="<?php echo plugins_url('/'.MENTOR_CRM_FOLDER.'/assets/dhara-frente.svg'); ?>">
            		<span class="title-images">FRENTE</span>
                    <button type="button" class="select_file" data-inputid="foto2">SUBIR FOTO</button>
            		<input type="file" name="lead_image[]" accept="image/*" class="check-required ocultar_archivo" id="foto2">
                    <span id="foto2_name"></span>
            	</label>
            </div>
		</div>
	</div>
	<div class="mentor-lead-box mentor-submit-area">
		<div class="crm-row">
            <div class="crm-col form-wrap">
                <textarea name="comments" placeholder="MENSAJE"></textarea>
                <p class="required-text">&#42; Campos obligatorios</p>
                <p><label><input type="checkbox" name="tag-age" value="1" required="true" checked="true"> SOY MAYOR DE 18 AÑOS</label></p>
                <p><label><input type="checkbox" name="tag-terms" value="1" required="true" checked="true">ACEPTO <a href="<?php echo $terms_url; ?>" target="_blank">TÉRMINOS Y CONDICIONES</a></label></p>
                <button type="submit" class="mentor-crm-submit">CONTINUAR</button>
            </div>
		</div>
	</div>
	</form>
</div>
<div class="mentor-crm-modal-response">
	<div class="mentor-crm-modal-body">
        <button class="mentor-crm-modal-close">&times;</button>
        <img src="<?php echo plugins_url('/'.MENTOR_CRM_FOLDER.'/assets/dhara-thanks.png'); ?>">
		<h1>Gracias</h1>
		<h2>Te contactaremos para confirmar tu cita.</h2>
	</div>
</div>
<script type="text/javascript">
jQuery(document).ready(function($){
	$('input[name="tag-consultatipo"]').click(function(){
		if ($(this).val() == 1) {
			$('.mentor-crm-images-tag').slideDown()
			//$('.check-required').prop('required',true)
		}else{
			//$('.check-required').prop('required',false)
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
	$('.mentor-lead-capture-form-ajax').submit(function(){
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
                    setTimeout("location.href = '"+data.payment_url+"'",3000)
                }else{
                    alert('Error, intenta de nuevo.')
                }
                $this.removeClass('form-ajax-sending')
                console.log(data)        
		    }
		});
		return false;
	})
})
</script>
	<?php
	return ob_get_clean();
}
add_shortcode( 'mentor-lead-capture-form', 'mentor_lead_capture_form' );