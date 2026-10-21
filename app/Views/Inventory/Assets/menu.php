<div class="modal fade text-left ui-options-modal" id="ModalMenu" tabindex="-1" role="dialog" aria-labelledby="labelMenu"
    data-bs-backdrop="static" data-bs-keyboard="false" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg" role="document">
        <div class="modal-content">
            
            <div class="modal-header">
                <h4 class="modal-title" id="labelMenu">Opciones del activo</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <div class="modal-body">
                <div id="mainMenu" class="ui-options-list">
                    <button type="button" class="btn btn-primary js-btn-asset-ips" data-id="">Direcciones IP</button>
                    <button type="button" class="btn btn-primary js-btn-asset-confidential-notes" data-id="">Notas privadas</button>
                    <button type="button" class="btn btn-primary js-btn-asset-windows-credentials" data-id="">Acceso a Windows</button>
                    <button type="button" class="btn btn-primary js-btn-asset-email-accounts" data-id="">Cuentas de correo</button>
                    <button type="button" class="btn btn-primary js-btn-asset-equipment-user" data-id="">Usuario del equipo</button>
                    <button type="button" class="btn btn-primary js-btn-software" data-id="">Programas instalados</button>
                    <button type="button" class="btn btn-primary js-btn-code-inventory" data-id="">Generar etiqueta</button>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">
                    <i class="bx bx-x d-block d-sm-none"></i>
                    <span class="d-none d-sm-block">Cerrar</span>
                </button>
            </div>
            
        </div>
    </div>
</div>
<?php require('asset-ips.php'); ?>
<?php require('asset-confidential-notes.php'); ?>
<?php require('asset-windows-credentials.php'); ?>
<?php require('asset-email-accounts.php'); ?>
<?php require('asset-equipment-user.php'); ?>
<?php require('software.php'); ?>
