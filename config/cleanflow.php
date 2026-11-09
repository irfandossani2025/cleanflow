<?php
return ['sales_address'=>env('CLEANFLOW_SALES_ADDRESS','sales@techittechnologies.com'),'support_address'=>env('CLEANFLOW_SUPPORT_ADDRESS','support@techittechnologies.com'),'payments_address'=>env('CLEANFLOW_PAYMENTS_ADDRESS','payments@techittechnologies.com'),'grace_days'=>(int) env('CLEANFLOW_SUBSCRIPTION_GRACE_DAYS',7)];
