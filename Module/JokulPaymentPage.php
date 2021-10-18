<?php
  /**
   * Output HTML to display Jokul Checkout Payment page
   */

  // ## Output the basic static HTML part
  $urlpayment= get_post_meta('12', 'JCPage', true);

?>
  <div style="margin-top: 40px;">
    <button onclick="request()" class="button alt" type="button" name="button">Show Payment Page</button>
  </div>
  <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
  <script type="text/javascript">
  loadJokulCheckout('<?php echo $urlpayment; ?>');

  function request() {
      //show payment page
        loadJokulCheckout('<?php echo $urlpayment; ?>');
  }

  </script>
