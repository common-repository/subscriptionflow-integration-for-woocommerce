jQuery(document).ready(function( $ ){


    /**
     * 
     * Repeater fields on product edit page
     * 
     * 
    */
    $( '#add-row' ).on('click', function() {
        
        var row = $( '.empty-row.screen-reader-text' ).clone(true);
        row.removeClass( 'empty-row screen-reader-text' );
        row.insertBefore( '#wc-sf-repeatable-fieldset-one tbody>tr:last' );
        return false;
    });

    $( '.remove-row' ).on('click', function() {
        $(this).parents('tr').remove();
        return false;
    });



      /**
       * 
       * Repeater fields on product edit page
       * 
       * On change plan dropdown 
       * 
       * Send ajax request admin\wc-sf-ajax-request.php
       * 
      */

      $(document).on('change','.wc-sf-product-list-dropdown', function(e) {
         e.preventDefault(); 
         let $this=$(this)
         let wc_sf_product_id =$this.val();
   
         $.ajax({

            beforeSend: function() {

               $this.parent().siblings('.product-plan').append('<span class="spinner is-active" style="float: left;"></span>');
           },
            type : "post",
            dataType : "json",
            url : wc_sf_ajax_obj.ajax_url,
            data : {action: "wc_sf_get_product_plan", wc_sf_product_id : wc_sf_product_id},
            success: function(response) {

               $this.parent().next('.product-plan').children('.spinner').remove();

               if(response.status) {

                  $this.parent().next('.product-plan').children('.wc-sf-product-list-dropdown').html(response.res_data);

               }
               else {

                  alert("Something went wrong,Please refresh the page and try again!");

               }
            },
            error: function(xhr) {
               $this.parent().next('.product-plan').children('.spinner').remove();
            

            }
         })   
   
      });
     

      

      /**
       * 
       * Showing on SubscriptionFlow Setting Tabs
       * 
       * On Click sync Plans 
       * 
       * Send ajax request admin\wc-sf-ajax-request.php
       * 
      */

      $(document).on('click','.wc-sf-button', function(e) {
         e.preventDefault(); 
         let $this=$(this)
         let wc_sf_product_id =$this.val();
   
         $.ajax({

            beforeSend: function() {

               
               $this.parent().remove('.wc-sf-msg');
               $this.parent().append('<span class="spinner is-active"></span>');
           },
            type : "post",
            dataType : "json",
            url : wc_sf_ajax_obj.ajax_url,
            data : {action:  $this.attr('data-action')},
            success: function(response) {

               $this.parent().children('.spinner').remove();

               $this.parent().append(response.res_data);

               
            },
            error: function(xhr) {
               $this.parent().children('.spinner').remove();
            

            }
         })   
   
      });
     

});