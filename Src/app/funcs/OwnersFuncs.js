
function OpenOwnersGrid() {
    Ext.getCmp('MainObjectsPanel').removeAll();

    var OwnersGrid = new crm.view.Owners.OwnersGrid();

    var OwnersPanel = Ext.create('Ext.panel.Panel', {
        title: 'OwnersPanel',
        flex    : 1,
        //border: true,
        itemId : 'OwnersPanel',
        baseCls:'x-plain',
        layout: {
            type: 'hbox',
            align: 'stretch'
        }
    });

    var OwnersView = Ext.create('Ext.panel.Panel', {
        region  : 'OwnersView',
        title   : 'OwnersView',
        itemId  : 'OwnersView',
        width   : 200,
        //html    : '<p>OwnersView!</p>',
        baseCls:'x-plain',
        flex    : 1,
        items   : [
            //Ext.create('Ext.panel.Panel', ),
            //Ext.create('Ext.panel.Panel', ),
            /*Ext.create('Ext.panel.Panel', {
                title   : 'Описание',
                itemId  : 'OwnerObjDescr',
                html    : 'Описание',
                bodyPadding : 5,
                autoScroll  : true,
                maxHeight   : 200
            }),*/
            Ext.create('Ext.panel.Panel', {
                items:  [
                    {
                        itemId  : 'OwnerObjectId',
                        hidden  : true,
                        value   : 0
                    },
                    {
                        title       : 'Общие характеристики объекта',
                        itemId      : 'OwnerObjInfo',
                        html        : 'Информация по объекту',
                        border      : false,
                        bodyPadding : 10
                    },
                    {
                        title   : 'Телефон собственника, ссылка на источник',
                        itemId  : 'OwnerObjPhone',
                        html    : 'Телефон собственника, ссылка на источник',
                        border      : false,
                        bodyPadding : 5
                    },
                    {
                        title   : 'Описание',
                        itemId  : 'OwnerObjDescr',
                        html    : 'Описание',
                        bodyPadding : 5,
                        border      : false,
                        autoScroll  : true,
                        maxHeight   : 200
                    },
                    {
                        title   : 'Комментарии',
                        itemId  : 'OwnerObjCommentsList',
                        html    : 'Нет комментариев',
                        autoScroll  : true,
                        bodyPadding : 5,
                        //border      : false,
                        maxHeight   : 200
                    },
                    {
                        xtype       : 'fieldcontainer',
                        fieldLabel  : '',
                        hideLabel   : true,
                        header      : false,
                        border      : false,
                        layout      : 'hbox',
                        disabled    : true,
                        itemId      : 'OwnerObjCommentsContainer',
                        width       : 450,
                        padding     : '0 0 0 5',
                        items: [
                            {
                                name        : 'OwnerObjCommentsText',
                                itemId      : 'OwnerObjCommentsText',
                                xtype       : 'textfield',
                                width       : 300,
                                blankText   : 'Необхоdddть поле'
                            },
                            Ext.create('Ext.Button', {
                                itemId  : 'OwnerObjCommentsAddBtn',
                                //disabled: true,
                                hidden  : false,
                                text    : 'добавить',
                                //padding : '0 0 0 10',
                                handler : function() {
                                    var btn = Ext.ComponentQuery.query('#OwnerObjCommentsAddBtn')[0];
                                    var OwnerObjCommentsText = Ext.ComponentQuery.query('#OwnerObjCommentsText')[0];
                                    //console.log(OwnerObjCommentsText.getValue());
                                    //console.log(GlobVars.Temp.OpenedSobObject);
                                    if(OwnerObjCommentsText.getValue().length > 0) {
                                        btn.setDisabled(true); // чтобы не было внезапных повторов
                                        Ext.Ajax.request({
                                            url     : OwnersSaveComment,
                                            params  : {
                                                ObjectId     : GlobVars.Temp.OpenedSobObject,
                                                CommentsText : OwnerObjCommentsText.getValue()
                                            },
                                            success: function(response, opts) {
                                                var obj = Ext.decode(response.responseText);
                                                UpdateSobCommentsContainer(GlobVars.Temp.OpenedSobObject);
                                                OwnerObjCommentsText.setValue('');
                                                btn.setDisabled(false);
                                            },
                                            failure: function(response, opts) {
                                                //todo ??
                                                //Op_ErrorStop('ошибка там-то....');
                                            }
                                        });
                                    }
                                }
                            })
                        ]
                    }
                ]


            })
        ]
    });

    Ext.ComponentQuery.query('#MainObjectsPanel')[0].add( OwnersPanel );
    Ext.ComponentQuery.query('#OwnersPanel')[0].add( OwnersGrid );
    Ext.ComponentQuery.query('#OwnersPanel')[0].add( OwnersView );


    OwnersGrid.store.setProxy( OwnersGridProxy ); // ставим url на активные объекты
    OwnersGrid.store.load();

}


