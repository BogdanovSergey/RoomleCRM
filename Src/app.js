Ext.define('crm.button.UploadButton',{
    extend: 'Ext.ux.upload.Button',
    requires : [
        'Ext.ux.upload.plugin.Window'
    ],
    alias : 'widget.ObjectUploadButton',
    text : 'Select files',
    SelectedObjectId     : 0,
    plugins: [{
        ptype   : 'ux.upload.window',
        pluginId: 'pid',
        width   : 320,
        height  : 350
    }],
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
            UpdateObjectImagesDataView( Ext.widget('ObjectUploadButton').SelectedObjectId ); // обновляем превьюшки по выбранному объекту
            console.log('uploadcomplete: ' + Ext.widget('ObjectUploadButton').SelectedObjectId );
        },
        scope: this
    }
});

Ext.application({
    requires    : ['Ext.container.Viewport'],
    name        : 'crm',
    appFolder   : 'app',
    controllers : ['Controller'],
    //autoCreateViewport: true,
    launch      : function() {
        GetFullUserInfo();  // загружаем все параметры текущего пользователя (id, права и т.д.) в GlobVars
        GetSysParams();     // загружаем системные параметры в GlobVars из тбл. SysParams
        Ext.create('Ext.container.Viewport', {
            id      : 'AppViewport',
            layout  : 'fit',
            items   : [
                {
                    id      : 'AppViewportPanel',
                    xtype   : 'panel',
                    border  : false,
                    layout  : {
                        type    : 'vbox',
                        align   : 'stretch'
                    },
                    items: [
                            Ext.widget('MainPanel'),
                            //Ext.widget('ObjectsGrid')
                            Ext.create('Ext.panel.Panel', {
                                id      : 'MainObjectsPanel',
                                layout  : 'fit',
                                flex    : 1,
                                items   : [{
                                    id      : 'MainObjectsCityGrid',
                                    xtype   : 'ObjectsGrid'
                                }]
                            })
                    ]
                }
            ]
        });

    }
});

