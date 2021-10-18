<?php

if ( isset($_GET['notif']) )
{
		switch ( strtolower($_GET['notif']) )
		{

				case "payment":
          require_once dirname( __FILE__ ) . '../../Notif/JokulCheckoutPayment.php';
				break;

				case "qris":
          require_once dirname( __FILE__ ) . '../../Notif/JokulCheckoutQris.php';
				break;

				default:
						echo "Stop : Request Not Recognize";
				break;
		}
}
