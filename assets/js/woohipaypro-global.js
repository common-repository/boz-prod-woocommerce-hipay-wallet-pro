jQuery.noConflict();
jQuery(document).ready(function($){
    
    if($('#woohipaypro-restricted-notice').length){
        
        function showRestrictedNotice(){
            
            $('#woohipaypro-restricted-notice').fadeIn(1000, function(){
                
                setTimeout(function(){
                
                    $('#woohipaypro-restricted-notice').fadeOut(500);

                }, 10000);
                
            });
            
        }
        
        setTimeout(function(){
                
            showRestrictedNotice();

        }, 2000);
        
    }
    
    if($('#woohipaypro-user-subscriptions-orders').length){
        
        if($('.woohipaypro-hidden-tr').length){
            
            $('.woohipaypro-hidden-tr').slideUp(1);
            
        }
        
        $('.woohipaypro-subscription-id').click(function(){
            
            var $sub_id = $(this).attr('id');
            
            $sub_id     = $sub_id.split('_');
            
            $sub_id     = $sub_id[1];
            
            if($('*[data-sub="'+$sub_id+'"]').hasClass('woohipaypro-hidden-tr')){
                
                $('*[data-sub="'+$sub_id+'"]').slideDown(150, function(){
                    
                    $('*[data-sub="'+$sub_id+'"]').removeClass('woohipaypro-hidden-tr');
                    
                });
                
            }else{
                
                $('*[data-sub="'+$sub_id+'"]').slideUp(50, function(){
                    
                    $('*[data-sub="'+$sub_id+'"]').addClass('woohipaypro-hidden-tr');
                    
                });
                
            }
                        
        });
                
    }
    
    if($('#woohipaypro-all-tokens').length){
        
        if($('#woohipaypro-tokens-list-dark').length){
            
            $('#woohipaypro-tokens-list-dark').slideUp(1);
            
            $('#woohipaypro-all-tokens > a').hover(function(){
                
                $('#woohipaypro-tokens-list-dark').slideDown(250);
                
            }, function(){
                
                $('#woohipaypro-tokens-list-dark').slideUp(100);
                
            });
            
        }else if($('#woohipaypro-tokens-list-light').length){
            
            $('#woohipaypro-tokens-list-light').slideUp(1);
            
            $('#woohipaypro-all-tokens > a').hover(function(){
                
                $('#woohipaypro-tokens-list-light').slideDown(250);
                
            }, function(){
                
                $('#woohipaypro-tokens-list-light').slideUp(100);
                
            });
            
        }
        
    }
    
    if($('.woohipaypro-button-order').length){
        
        $('.woohipaypro-button-order').click(function(){
            
            var $product_id = $(this).attr('data-product');
            var $price      = $(this).attr('data-price');
            var $target     = $(this).attr('data-target');
            
            var woohipaypro_order = {
                action:                     'woohipaypro_make_order',
                woohipaypro_product_id:     $product_id,
                woohipaypro_price:          $price,
                woohipaypro_target:         $target
            };
                        
            $.ajax({
                type:       'POST',
                url:        woohipapro_ajax.ajax_url,
                dataType:   "json",
                data:       woohipaypro_order,
                success: function(data) {

                    dataResult = $.parseJSON(data);

                    if(dataResult.label == 'ok'){

                        window.location.replace(dataResult.action);

                    }else{

                        alert(dataResult.action);

                    }
                }
            });
            
        });
        
    }
    
    if($('.woohipaypro-stop-formula').length){
        
        function woohipayproStopFormula($id,$label,$warning,$confirm,$error,$cancel,$stop,$cog,$res){
            
            $('body').append('<div id="woohipaypro-black-bg"><div id="content"><div id="woohipaypro-stop"><p id="label">'+$label+'</p><p class="psmall">'+$warning+'</small></p><div id="cog"><img src="'+$cog+'" alt="cog" id="woohipaypro-cog"/><p>'+$res+'</p></div><p id="stop"><a href="javascript:void(0);" id="btn-stop" data-formula="'+$id+'">'+$stop+'</a></p><p id="confirm">'+$confirm+'</p><p id="error">'+$error+'</p><p id="cancel"><a href="javascript:void(0);" id="woohipaypro-cancel-stop-formula">'+$cancel+'</a></p></div></div></div>');
            
            $('#woohipaypro-black-bg').on('click', function(e) {
                
                if(e.target !== this){
                    
                    return;
                    
                }else{
                    
                    $('#woohipaypro-black-bg').fadeOut(250, function(){
                    
                        $('#woohipaypro-black-bg').remove();

                    });
                    
                }
                
            });
            
            $('#woohipaypro-cancel-stop-formula').click(function(){
                
                $('#woohipaypro-black-bg').fadeOut(250, function(){
                    
                    $('#woohipaypro-black-bg').remove();

                });
                
            });
            
            $('#woohipaypro-black-bg #content #woohipaypro-stop #stop #btn-stop').click(function(){
                
                var $formula_id = $(this).attr('data-formula');
                                
                $('#woohipaypro-black-bg #content #woohipaypro-stop #label').fadeOut(50);
                
                $('#woohipaypro-black-bg #content #woohipaypro-stop .psmall').fadeOut(50);
                
                $('#woohipaypro-black-bg #content #woohipaypro-stop #stop').fadeOut(50);
                
                $('#woohipaypro-black-bg #content #woohipaypro-stop #cancel').fadeOut(50);
                
                $('#woohipaypro-black-bg #content #woohipaypro-stop #cog').fadeIn(250);
                
                var woohipaypro_stop = {
                    action:                     'woohipaypro_stop_formula',
                    woohipaypro_formula_id:     $formula_id
                };
                
                $.ajax({
                    type:       'POST',
                    url:        woohipapro_ajax.ajax_url,
                    dataType:   "json",
                    data:       woohipaypro_stop,
                    success: function(data) {

                        dataResult = $.parseJSON(data);
                        
                        if(dataResult.label == 'ok'){

                            $('#woohipaypro-black-bg #content #woohipaypro-stop #cog').fadeOut(50);
                                                        
                            $('#woohipaypro-black-bg #content #woohipaypro-stop p#confirm').fadeIn(250);
                            
                            $('#woohipaypro-black-bg #content #woohipaypro-stop #cancel a').html('OK');
                            
                            $('#woohipaypro-black-bg #content #woohipaypro-stop #cancel').fadeIn(250);
                            
                            $('#formula_'+$formula_id).html($('#formula_'+$formula_id).attr('data-inactive'));
                            
                        }else if(dataResult.label != 'ok'){

                            $('#woohipaypro-black-bg #content #woohipaypro-stop #cog').fadeOut(50);
                            
                            $('#woohipaypro-black-bg #content #woohipaypro-stop p#error').fadeIn(250);
                            
                            $('#woohipaypro-black-bg #content #woohipaypro-stop #cancel').fadeIn(250);

                        }
                    }
                });
                                
            });
            
        }
        
        $('.woohipaypro-stop-formula').click(function(){
            
            var $ID         = $(this).attr('id');
            
            var $sub_id     = $ID.split('_');
            
            $sub_id         = $sub_id[1];
            
            var $label      = $(this).attr('data-label')+'<br/><strong>'+$sub_id+'</strong>';
            
            var $warning    = $(this).attr('data-warning');
            
            var $confirm    = $(this).attr('data-confirm');
            
            var $error      = $(this).attr('data-error');
            
            var $cancel     = $(this).attr('data-cancel');
            
            var $stop       = $(this).attr('data-stop');
            
            var $cog        = $(this).attr('data-cog');
            
            var $res        = $(this).attr('data-res');
            
            woohipayproStopFormula($sub_id,$label,$warning,$confirm,$error,$cancel,$stop,$cog,$res);
            
        });
        
    }
    
});