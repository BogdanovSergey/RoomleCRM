Ext.define('crm.view.Mail.MailGrid', {
    extend      : 'Ext.grid.Panel',
    id          : "MailGrid",
    alias       : 'widget.MailGrid',
    stripeRows  : false,
    //width : 200,
    height: 200,
    //forceFit: true, // для растягивания колонок
    initComponent: function() {
        Ext.apply(this, {
            store : 'Mail.MailGridStore',
            columns : [
            {
                text     : '№',
                dataIndex: 'id',
                width:   40
            },
            {
                text     : 'От',
                dataIndex: 'MailFrom',
                width:   180
                //flex     : 1
            },
            {
                text     : 'Тема',
                dataIndex: 'Subject',
                width:   200,
                flex     : 1
            },
            {   text     : 'Добавлен',
                dataIndex: 'AddedDate',
                width    : 80
                //flex     : 1
            },
            {   text     : 'Удален',
                dataIndex: 'ArchivedDate',
                width    : 80,
                hidden   : true
            },
            {   xtype   : 'actioncolumn',
                text    : 'Удалить',
                width   : 30,
                dataIndex: 'DeleteColumn',
                hidden  : true,
                items   : [{
                    icon:'icons/cross.png',
                    handler:function (grid, rowIndex, colIndex) {
                        var rec = grid.getStore().getAt(rowIndex);
                        ArchivateUserById( rowIndex, rec.get('id'), rec.get('LastName') );
                    }
                }]
            },
            {   xtype       : 'actioncolumn',
                width       : 30,
                text        : 'Восстановить',
                dataIndex   : 'RestoreColumn',
                hidden      : true,
                items       : [{
                    icon:'icons/arrow_undo.png',
                    handler:function (grid, rowIndex, colIndex) {
                        var rec = grid.getStore().getAt(rowIndex);
                        RestoreUserById( rowIndex, rec.get('id'), rec.get('LastName') );
                    }
                }]
            }
            ],
            dockedItems : [
                {
                    xtype       : 'toolbar',
                    hidden      : true,
                    dock        : 'bottom',
                    items: [
                        {
                            text    : '',
                            iconCls : 'x-tbar-loading',
                            hidden  : false,
                            handler : function() {
                                Ext.getCmp('MailGrid').getStore().load();
                            }
                        }
                        /*{
                            text    : 'select all',
                            hidden: true,
                            handler : function() {
                                console.log( Ext.getCmp('MailGrid').getView().getNodes() );

                            }
                        },
                        ,*/

                        //{ xtype: 'tbfill' },
                        /*{
                            xtype   : 'tbtext',
                            id      : 'ObjectsVtorCountLabel',
                            text    : 'Объекты не загружены', // сменится на "Всего объектов: ххх" через контроллер
                            style   : {
                                //textalign : 'right'
                            }
                        }*/

                    ]
                }
            ]
        });

        this.callParent(arguments);
    },

    tbar:[
        {
            text    : '',
            iconCls : 'x-tbar-loading',
            hidden  : false,
            handler : function() {
                Ext.getCmp('MailGrid').getStore().load(); // обновляем список
            }
        },
        '-',
        {
            xtype           : 'radiogroup',
            fieldLabel      : ' ',
            labelSeparator  : ' ',
            hidden      : true,
            items: [{
                boxLabel    : 'Рабочие',
                name        : 'UsersWorkingSet',
                id          : 'UsersWorkingSetWorking',
                inputValue  : 'UsersWorkingSetWorking',
                width       : 70,
                padding     :  '0 0 0 0',
                checked     : true,
                handler: function() {
                    if (Ext.getCmp('UsersWorkingSetWorking').getValue()) {
                        var grid = Ext.getCmp('UsersGrid');
                        grid.store.setProxy( ActiveUsersGridProxy );   // меняем ссылку для забора только АКТИВНЫХ объектов
                        grid.getStore().load();                            // перегружаем грид
                        grid.columns[ findColumnIndex(grid.columns, 'AddedDate') ].setVisible(true); // показываем колонку AddedDate
                        grid.columns[ findColumnIndex(grid.columns, 'ArchivedDate') ].setVisible(false); // прячем колонку ArchivedDate
                        grid.columns[ findColumnIndex(grid.columns, 'DeleteColumn') ].setVisible(true); // показываем колонку DeleteColumn
                        grid.columns[ findColumnIndex(grid.columns, 'RestoreColumn') ].setVisible(false); // прячем колонку RestoreColumn
                    }
                    return;
                }
            }, {
                boxLabel    : 'Архивные',
                name        : 'UsersWorkingSet',
                inputValue  : 'UsersWorkingSetArchive',
                id          : 'UsersWorkingSetArchive',
                padding     :  '0 0 0 0',
                handler: function() {
                    if (Ext.getCmp('UsersWorkingSetArchive').getValue()) {
                        var grid = Ext.getCmp('UsersGrid');
                        grid.store.setProxy( ArchivedUsersGridProxy ); // меняем ссылку для забора только АРХИВНЫХ объектов
                        grid.getStore().load();                            // перегружаем грид
                        grid.columns[ findColumnIndex(grid.columns, 'AddedDate') ].setVisible(false);   // прячем колонку AddedDate
                        grid.columns[ findColumnIndex(grid.columns, 'ArchivedDate') ].setVisible(true);    // показываем колонку ArchivedDate
                        grid.columns[ findColumnIndex(grid.columns, 'DeleteColumn') ].setVisible(false);  // прячем колонку DeleteColumn
                        grid.columns[ findColumnIndex(grid.columns, 'RestoreColumn') ].setVisible(true);   // показываем колонку RestoreColumn

                    }
                    return;
                }
            }]
        }
    ],

    listeners: {
        dblclick : {
            fn: function() {                    // Открытие при двойном клике

                var selectedRecord = Ext.getCmp('MailGrid').getSelectionModel().getSelection()[0];

                Ext.Ajax.request({
                    url: OpenMailUrl,
                    params  : {
                        EmailId: selectedRecord.data.id
                    },
                    success: function(response, opts) {
                        var obj = Ext.decode(response.responseText);
                        if(obj.success == true) {
                            console.log(obj);
                            Ext.getCmp('MailListWindow_Body').update(obj.message);
                            Ext.getCmp('MailGrid').getStore().load(); // обновляем список
                        } else {
                            alert(Words_SystemErrorMsg + '109' + '\n' + Words_CallProgrammerMsg + '\n\n' + obj.message);
                        }
                    },
                    failure: function(response, opts) {
                        alert(Words_SystemErrorMsg + '110' + '\n' + Words_CallProgrammerMsg);
                    }
                });


                /*
                var selectedRecord = Ext.getCmp('UsersGrid').getSelectionModel().getSelection()[0];
                var UserWindow = Ext.widget('UserWindow');
                var UserForm = Ext.getCmp('UserForm');
                //Ext.apply(Ext.getCmp('ObjectPhotosUploadBtn'), {SelectedObjectId : selectedRecord.data.id}); // вставляем id объекта в свойство кнопки-загрузчика
                UserWindow.setTitle(Words_EditUserTitle + ' №' + selectedRecord.data.id); // заголовок - редактирование
                UserWindow.show();            // открываем окно
                UserForm.getForm().reset();   // сбрасываем предыдущую форму
                UserForm.getForm().load({     // загружаем данные в форму
                    waitMsg :'Открывается сотрудник № ' + selectedRecord.data.id,
                    url     : 'Super.php',
                    method  : 'GET',
                    params  : {
                        id  : selectedRecord.data.id,
                        Action: 'OpenUser'},
                    success: function(response, options) {
                    }
                });*/
            },
            // You can also pass 'body' if you don't want click on the header or
            // docked elements
            element: 'body'
        }
    }

});
