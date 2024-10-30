jQuery.noConflict();
jQuery(document).ready(function($){
    
    function parseURLParams(url){
        
        var queryStart = url.indexOf("?") + 1,
            queryEnd   = url.indexOf("#") + 1 || url.length + 1,
            query = url.slice(queryStart, queryEnd - 1),
            pairs = query.replace(/\+/g, " ").split("&"),
            parms = {}, i, n, v, nv;

        if (query === url || query === "") return;

        for (i = 0; i < pairs.length; i++) {
            nv = pairs[i].split("=", 2);
            n = decodeURIComponent(nv[0]);
            v = decodeURIComponent(nv[1]);

            if (!parms.hasOwnProperty(n)) parms[n] = [];
            parms[n].push(nv.length === 2 ? v : null);
        }
        
        return parms;
    }
    
    var $current_uri    = window.location.href;
    
    const $urlGETparams   = parseURLParams($current_uri);
    
    /* Check if url is ok to display the loader */
    if($urlGETparams && Object.keys($urlGETparams).length == 2 && typeof($urlGETparams.woohipaywalletpro) !== "undefined" && typeof($urlGETparams.order) !== "undefined" && $urlGETparams.woohipaywalletpro == "payment-treatment" && $urlGETparams.order != "" && Number.isInteger(parseInt($urlGETparams.order))){
        
        if($('.woocommerce').length){
            
            console.log('fuck you');
            
            $('.woocommerce-form-coupon-toggle').css({'display':'none'});
            $('.checkout_coupon woocommerce-form-coupon').css({'display':'none'});
            $('[name="checkout"]').css({'display':'none'});
            
            $('#woohipaypro-loader').css({'display':'block'});
            
            setTimeout(function(){
                
                document.location.reload(true);
                
            }, 10000);
                        
        }
        
    }
    
});