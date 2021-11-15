Ext.define('crm.view.ObjectPhotosTab', {
    extend      : 'Ext.Panel',
    id          : "ObjectPhotosTab",
    alias       : 'widget.ObjectPhotosTab',
    disabled    : true,
    collapsible : true,
    frame       : true,
    title       : 'Фотографии',
    initComponent: function() {
        Ext.apply(this, {
            id          : "ObjectPhotosTab",
            items: [
                {
                    // кнопка-загрузка
                    xtype : 'ObjectUploadButton',
                    itemId: 'ObjectUploadButton',
                    text  : 'Выбрать фотографии для загрузки',
                    uploader: {
                        url             : 'Super.php?UrlFrom_ObjectPhotosTab.js_isNotSet',
                        uploadpath      : '/Root/files',
                        autoStart       : true,
                        max_file_size   : '2020mb',
                        statusQueuedText: 'Ready to upload',
                        statusUploadingText : 'Uploading ({0}%)',
                        statusFailedText    : '<span style="color: red">Error</span>',
                        statusDoneText      : '<span style="color: green">Complete</span>',
                        statusInvalidSizeText: 'File too large',
                        statusInvalidExtensionText: 'Invalid file type'
                    },
                    listeners: {
                        beforeupload: function(uploader, file) {
                            console.log('---->beforeupload');
                        },
                        uploadcomplete: function(uploader, success, failed) {
                            Ext.ComponentQuery.query('#ObjectDataView')[0].getStore().load(); // обновляем список
                            console.log('<-----uploadcomplete - обновляем список #ObjectDataView');
                        }
                    }
                },
                Ext.widget('ObjectDataView')
        ]});

        this.callParent(arguments);
    }
});

