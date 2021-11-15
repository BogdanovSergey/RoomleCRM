Ext.define('crm.view.ObjectPhotosUploadBtn',{
    extend: 'Ext.ux.upload.Button',
    text        : 'Select files',
    id          : 'ObjectPhotosUploadBtn',
    alias       : 'widget.ObjectPhotosUploadBtn',
    SelectedObjectId     : 0,
    autoRender  : true,
    //renderTo: 'TempDiv',
    hidden      : false,
    //singleFile: true,
        //plugins: [],
    plugins: [{
        ptype   : 'ux.upload.window',
        title   : 'Upload',
        width   : 320,
        height  : 350,
        pluginId: 'pid'
    }],
    uploader: {
        url             : MainSiteUrl + 'getimages.php?a=a&Object=',
        uploadpath      : '/Root/files',
        autoStart       : true,
        max_file_size   : '2020mb',
        statusQueuedText: 'Ready to upload',
        statusUploadingText: 'Uploading ({0}%)',
        statusFailedText: '<span style="color: red">Error</span>',
        statusDoneText: '<span style="color: green">Complete</span>',
        statusInvalidSizeText: 'File too large',
        statusInvalidExtensionText: 'Invalid file type'
    },
    listeners: {
        filesadded: function(uploader, files) {
            console.log('filesadded');
            return true;
        },
        beforeupload: function(uploader, file) {
            //console.log('beforeupload');
        },
        fileuploaded: function(uploader, file) {
            //console.log('fileuploaded');
        },
        uploadcomplete: function(uploader, success, failed) {
            if(failed.length == 0) {
                uploader.store.loadRecords(Array(), {addRecords: false});
            }
            UpdateObjectImagesDataView( Ext.widget('ObjectUploadButton').SelectedObjectId, null, GlobVars.OpenedRealtyType); // обновляем превьюшки по выбранному объекту
            console.log('uploadcomplete');
        },
        scope: this
    }
//    initComponent: function() {
        /*this.plugins = {
            ptype   : 'ux.upload.window',
            title   : 'Upload',
            width   : 320,
            height  : 350,
            pluginId: 'pid'
        };*/
          /*Ext.apply(this, {
            plugins: {
                ptype   : 'ux.upload.window',
                title   : 'Upload',
                width   : 320,
                height  : 350,
                pluginId: 'pid'
            }});*/
        /*this.plugins = [{
            ptype   : 'ux.upload.window',
            title   : 'Upload',
            width   : 320,
            height  : 350,
            pluginId: 'pid'
        }];*///[Ext.create('/ext-4.2.1.883/src/ux/upload/plugin/window', {clicksToEdit: 1})];
        /*this.plugins = {
            ptype   : 'ux.upload.window',
            title   : 'Upload',
            width   : 320,
            height  : 350,
            pluginId: 'pid'
        };
        this.uploader ={
            url             : MainSiteUrl + 'getimages.php?a=a&Object=',
                uploadpath      : '/Root/files',
                autoStart       : true,
                max_file_size   : '2020mb',
                statusQueuedText: 'Ready to upload',
                statusUploadingText: 'Uploading ({0}%)',
                statusFailedText: '<span style="color: red">Error</span>',
                statusDoneText: '<span style="color: green">Complete</span>',
                statusInvalidSizeText: 'File too large',
                statusInvalidExtensionText: 'Invalid file type'
        };
        this.callParent();*/
//    }
    //    Ext.apply(this, {
            /*plugins: [{
                ptype   : 'ux.upload.window',
                title   : 'Upload',
                width   : 320,
                height  : 350,
                pluginId: 'pid'
            }],
            uploader: {
                url             : MainSiteUrl + 'getimages.php?a=a&Object=',
                uploadpath      : '/Root/files',
                autoStart       : true,
                max_file_size   : '2020mb',
                statusQueuedText: 'Ready to upload',
                statusUploadingText: 'Uploading ({0}%)',
                statusFailedText: '<span style="color: red">Error</span>',
                statusDoneText: '<span style="color: green">Complete</span>',
                statusInvalidSizeText: 'File too large',
                statusInvalidExtensionText: 'Invalid file type'
            },
            listeners: {
                filesadded: function(uploader, files) {
                    console.log('filesadded');
                    return true;
                },
                beforeupload: function(uploader, file) {
                    //console.log('beforeupload');
                },
                fileuploaded: function(uploader, file) {
                    //console.log('fileuploaded');
                },
                uploadcomplete: function(uploader, success, failed) {
                    if(failed.length == 0) {
                        uploader.store.loadRecords(Array(), {addRecords: false});
                    }
                    UpdateObjectImagesDataView( Ext.getCmp('ObjectPhotosUploadBtn').SelectedObjectId ); // обновляем превьюшки по выбранному объекту
                    console.log('uploadcomplete');
                },
                scope: this
            }*/
        //});

        //this.callParent();
    //}
})