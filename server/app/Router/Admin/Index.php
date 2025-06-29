<?php
if (!defined('ANON_ALLOWED_ACCESS')) exit;

if (Anon_Check::isLoggedIn()) {
    renderAdminIndex();
}

function renderAdminIndex()
{
?>
<div class="m-4 max-w-screen-xl mx-auto w-full">

</div>
<?php
}
