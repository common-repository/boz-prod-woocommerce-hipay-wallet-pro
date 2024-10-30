jQuery.noConflict();
jQuery(document).ready(function($){
    
    if($('.woohipayhidden').length){
        
        if($('#type_hipaysubscription').val() == 0){
            
            $('.woohipayhidden').parent().css({'display':'none'});
            
        }
        
        $('#type_hipaysubscription').change(function(){
        
            if($(this).val() == 0){

                $('.woohipayhidden').parent().fadeOut(250);

            }else if($(this).val() == 1){

                $('.woohipayhidden').parent().slideDown(150);

            }

        });
                
    }
    
    if($('.woohipayhidden_aff').length){
        
        if($('#affiliates_hipaysubscription').val() == 0){
            
            $('.woohipayhidden_aff').parent().css({'display':'none'});
            
        }
        
        $('#affiliates_hipaysubscription').change(function(){
        
            if($(this).val() == 0){

                $('.woohipayhidden_aff').parent().fadeOut(250);

            }else if($(this).val() == 1){

                $('.woohipayhidden_aff').parent().slideDown(150);

            }

        });
                
    }
    
    if($('#woohipaypro-admin-cancel-subscription').length){
        
        $('#woohipaypro-admin-cancel-subscription').click(function(){
            
            var $sub_id = $(this).attr('data-subscription');
            
            var $error  = $(this).attr('data-error');
            
            var adminCancelSub = {
                action:                     'woohipaypro_admin_stop_formula',
                woohipaypro_subcription:    $sub_id
            };

            $.post(ajaxurl, adminCancelSub, function(response){

                if(response == "ok"){

                    location.reload(true);

                }else{

                    alert($error);

                }

            });
            
        });
        
    }
    
});