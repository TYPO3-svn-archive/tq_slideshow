<?php
class tx_tqslideshow_directory_uploader {

    /**
     * The Ajax Controller to the module
     *
     * @var string
     */
    protected $_ajaxController  = 'ajax.php?ajaxID=tqslideshow::collection';


    /**
     * The Ajax Controller to the module
     *
     * @var string
     */
    protected $_rootNode  = 'fileadmin/';


    /**
     * @param $PA
     * @param $fobj
     * @return string
     */
    public function field($PA, $fobj) {
        $recordUid  = (int)$PA['row']['uid'];
        $js = '
            <script type="text/javascript">
                var urlValue    = null;

                var saveButton = new Ext.Button({
                     text:"Submit",
                     disabled:true,
                     handler: function() {
                       var node  = tree.getSelectionModel().getSelectedNode();
                       if(node) {
                        Ext.Ajax.request({
                            url: "'.$this->_ajaxController.'&cmd=saveImage",
                            method: "GET",
                            params: {
                                path: node.attributes.id,
                                uid: "'.$recordUid.'"
                            },
                            callback: function( object, connection, resp ) {
                                if ( resp.responseText == "true" ) {
                                  var form    = Ext.getBody().down("form");
                                  form.dom.submit();
                                } else {

                               }
                            }
                        });
                      }
                    }
                });

                var tree = new Ext.tree.TreePanel({
                    useArrows: true,
                    autoScroll: true,
                    animate: true,
                    enableDD: true,
                    containerScroll: true,
                    width: 300,
                    height: 300,
                    border: false,
                    // auto create TreeLoader
                    dataUrl: "'.$this->_ajaxController.'&cmd=getFolder",
                    root: {
                        nodeType: "async",
                        text: "'.$this->_rootNode.'",
                        draggable: false,
                        id: "'.$this->_rootNode.'"
                    },
                    rootVisible: false,
                    listeners: {
                        beforeclick: function(n) {
                            if(n.attributes.cls == "file" ) {
                                return false
                            }
                            saveButton.setDisabled(false);
                            return true;
                        },
                        click: function(n) {

                        }
                    }
                });

                var frmConfirm = new Ext.Window({
                    xtype: "form",
                    id :"directoryUploaderFrame",
                    width: 400,
                    minHeight: 200,
                    height: 400,
                    modal: true,
                    closeAction: "hide",
                    resizable: false,
                    bodyStyle: "padding: 15px; 5px 15px 30px",
                    title: "Verzeichnis uploader",
                    items: [tree],
                     buttons: [saveButton,{
                        text: "Close",
                        handler: function(){
                            frmConfirm.hide();
                        }
                    }],
                    listeners: {
                        beforeclose: function() {
                             frmConfirm.hide();
                        }
                    }
                });
                function tqShow() {
                   frmConfirm.show();
                }


            </script>
        ';

        /*
        $lnkfld  = '<div class="nwsupld-form">';
        //-- input field for manual input of URL
        $lnkfld .= '<div class="insrturl">';
        $lnkfld .= '<input id="directoryUploader" style="width:200px" name="' . $PA['itemFormElName'] . '" ';
        $lnkfld .= 'value="' . htmlspecialchars($PA['itemFormElValue']) . '" ';
        $lnkfld .= 'onchange="' . htmlspecialchars(implode('', $PA['fieldChangeFunc'])) . '" readonly/>';
        $lnkfld .= '</div>';
        */

        $lnkfld = '';
        //-- upload file dialogue
        $lnkfld .= '<div class="process">';
        $lnkfld .= '<input value="Dirctory selection" name="Upload"';
        $lnkfld .= 'onchange="' . implode('', $PA['fieldChangeFunc']) . '" ';
        $lnkfld .= 'onclick="tqShow()" ';
        $lnkfld .= 'type="button" size="60" />';
        $lnkfld .= '</div></div>';
        return $js.$lnkfld;
    }

}


?>