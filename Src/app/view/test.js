Ext.define('crm.view.ObjectPhotosUploadBtn',{
    extend: 'Ext.ux.upload.Button',
    text        : 'Select files',
    id          : 'ObjectPhotosUploadBtn',
    alias       : 'widget.ObjectPhotosUploadBtn',
    SelectedObjectId     : 0,
    autoRender  : true,
    hidden      : false,
    initComponent: function() {
        this.plugins = {
            ptype   : 'ux.upload.window',
            title   : 'Upload',
            width   : 320,
            height  : 350,
            pluginId: 'pid'
        };
        this.uploader ={
            // exactly the same stuff
        };
        this.listeners = {
            // exactly the same stuff
        };

        this.callParent(arguments);
    }
})