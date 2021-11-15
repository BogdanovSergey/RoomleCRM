// главный список пользоавтелей
Ext.define('crm.view.ClientsGrid', {
    extend      : 'Ext.grid.Panel',
    id          : "ClientsGrid",
    alias       : 'widget.ClientsGrid',

    stripeRows  : false,
    /*stateId     : 'ClientsGridState',
    stateful    : true,
    stateEvents: ['columnresize', 'columnmove', 'show', 'hide'],*/
    initComponent: function() {
        Ext.apply(this, {
            store : 'ClientsGridStore',
            columns : [
            {
                text     : '№',
                dataIndex: 'id',
                width:   20
            },
            {   text     : 'Добавлен',
                dataIndex: 'AddedDate',
                width    : 80
            },
            {   text     : 'Удален',
                dataIndex: 'ArchivedDate',
                width    : 80,
                hidden   : true
            },
            {
                text     : 'Фамилия',
                dataIndex: 'LastName',
                //width:   40,
            },
            {
                text     : 'Имя Отчество',
                dataIndex: 'FirstName',
                //width:   60,
                flex     : 1
            },
            {
                text     : 'Тип',
                width    : 80,
                dataIndex: 'ClientType'
            },
            {
                text     : 'Моб. номер',
                //sortable : false,
                width    : 90,
                dataIndex: 'MobilePhone'
            },
            {
                text     : 'Birthday',
                //sortable : false,
                dataIndex: 'Birthday',
                hidden   : true
            },
            {
                text     : 'Email',
                dataIndex: 'Email',
                hidden   : true
            },
            {
                text     : 'Адрес объекта',
                dataIndex: 'ObjectLocation',
                flex     : 1
            },
            {
                text     : 'Source',
                dataIndex: 'Source',
                hidden   : true
            },
            {   xtype       : 'actioncolumn',
                text        : 'Удалить',
                width       : 30,
                sortable    : false,
                dataIndex   : 'DeleteColumn',
                items       : [{
                        //icon:'icons/cross.png',
                        getClass: function (value, meta, record, rowIndex, colIndex) {
                            /*if((CheckUserAccessRule('Users-All-ReadEditDeleteRestore') || CheckUserAccessRule('Users-All-Delete')) &&
                                !CheckUserAccessRule('Users-LimitByOwnGroup')) {
                                // право на всё или право на удаление без ограничения отделом
                                var cls = 'DeleteCls';
                            } else if(CheckUserAccessRule('Users-All-ReadEditDeleteRestore') && CheckUserAccessRule('Users-LimitByOwnGroup') && record.get('GroupId')== GlobVars.CurrentUser.GroupIdsArr[0] ) { // проверка только первой группы
                                // право на всё в рамках моего отдела
                                // TODO сделать цикл на проверку массива отделов #USERGROUPARR
                                var cls = 'DeleteCls';
                            } else if(CheckUserAccessRule('Users-All-Delete') && CheckUserAccessRule('Users-LimitByOwnGroup') && record.get('GroupId')== GlobVars.CurrentUser.GroupIdsArr[0] ) { // проверка только первой группы
                                // право на удаление в рамках моего отдела
                                var cls = 'DeleteCls';
                            } else {
                                var cls = 'InvisibleItem';  //ничего не показываем
                            }*/
                            var cls = 'DeleteCls';
                            return cls;
                        },
                        handler:function (grid, rowIndex, colIndex) {
                            var rec = grid.getStore().getAt(rowIndex);
                            ArchivateClientById( rowIndex, rec.get('id'), rec.get('LastName') );
                        }
                }]
            },
            {   xtype       : 'actioncolumn',
                width       : 30,
                text        : 'Восстановить',
                dataIndex   : 'RestoreColumn',
                sortable    : false,
                hidden      : true,
                items       : [{
                    //icon:'icons/arrow_undo.png',
                    getClass: function (value, meta, record, rowIndex, colIndex) {
                        /*if((CheckUserAccessRule('Users-All-ReadEditDeleteRestore') || CheckUserAccessRule('Users-All-Restore'))&& !CheckUserAccessRule('Users-LimitByOwnGroup')) {
                            var cls = 'RestoreCls';
                        } else if((CheckUserAccessRule('Users-All-ReadEditDeleteRestore') || CheckUserAccessRule('Users-All-Restore')) && CheckUserAccessRule('Users-LimitByOwnGroup') && record.get('GroupId')== GlobVars.CurrentUser.GroupIdsArr[0] ) { // проверка только первой группы
                            // TODO сделать цикл на проверку массива отделов #USERGROUPARR
                            var cls = 'RestoreCls';
                        } else {
                            var cls = 'InvisibleItem';  //ничего не показываем
                        }*/
                        var cls = 'RestoreCls';
                        return cls;
                    },
                    handler:function (grid, rowIndex, colIndex) {
                        var rec = grid.getStore().getAt(rowIndex);
                        RestoreClientById( rowIndex, rec.get('id'), rec.get('LastName') );
                    }
                }]
            }
            ],
            dockedItems : [
                /*{
                    xtype       : 'pagingtoolbar',
                    store       : 'ClientsGridStore',//Ext.data.StoreManager.lookup('ClientsGridStore'),
                    dock        : 'bottom',
                    displayInfo : true,
                    displayMsg  : 'Всего сотрудников: {2}',
                    emptyMsg    : 'Не найдено ни одного сотрудника'
                },*/
                {
                    xtype       : 'toolbar',
                    dock        : 'bottom',
                    items: [
                        {
                            text    : '',
                            iconCls : 'x-tbar-loading',
                            hidden  : false,
                            handler : function() {
                                Ext.getCmp('ClientsGrid').getStore().load();
                            }
                        },
                        {
                            text    : 'Excel',
                            iconCls : 'ExportToExcelCls',
                            hidden  : false,
                            handler : function() {                                        // TODO сделать сортировку по неск столбцам
                                if(typeof Ext.data.StoreManager.lookup('ClientsGridStore').sorters.items[0] !== "undefined") {
                                    var SortProp = Ext.data.StoreManager.lookup('ClientsGridStore').sorters.items[0].property;
                                }
                                if(typeof Ext.data.StoreManager.lookup('ClientsGridStore').sorters.items[0] !== "undefined") {
                                    var SortDir  = Ext.data.StoreManager.lookup('ClientsGridStore').sorters.items[0].direction;
                                }
                                var DownloadUrl = BuildClientExportBtnUrlString(MainAjaxDriver, ClientsGrid_Action, ClientsGrid_Active,
                                    'xls',
                                    SortProp,
                                    SortDir);
                                document.location = DownloadUrl;
                            }
                        },

                        { xtype: 'tbfill' },
                        {
                            xtype   : 'tbtext',
                            itemId  : 'ClientsListCountLabel',
                            text    : 'Клиенты не загружены', // сменится на "Всего ___: ххх" через контроллер
                            style   : {
                                //textalign : 'right'
                            }
                        }

                    ]
                }
            ]
        });

        this.callParent(arguments);
    },


    tbar: [{
        text    : 'Создать нового клиента',
        iconCls : 'AddCls',
        disabled: false,
        itemId  : 'Btn_CreateClient',
        handler : function() {
            var ClientWindow = Ext.widget('ClientWindow');
            //WhileOpeningTheUserForm();
            ClientWindow.setTitle(Words_CreateClientTitle);

            //Ext.getCmp('ObjectTabs').down('#ObjectPhotosTab').setDisabled(true); // новому объекту сначала нужно сохранить характеристики, затем открыть возм загрузки фото
            //Ext.getCmp('SquareLiving').setFieldLabel('Жилая'); // приводим в порядок форму (могли быть оставлны изменения при редактировании объектов)
            //Ext.getCmp('RoomsSellRow').setVisible(false);
            //Ext.apply(Ext.getCmp('RoomsSell'), {allowBlank: true}, {});



            ClientWindow.show();
        }
    },
    '-',
    {
        xtype           : 'radiogroup',
        fieldLabel      : ' ',
        labelSeparator  : ' ',
        items: [{
            boxLabel    : 'Рабочие',
            name        : 'ClientWorkingSet',
            id          : 'ClientWorkingSetWorking',
            inputValue  : 'ClientWorkingSetWorking',
            width       : 70,
            padding     :  '0 0 0 0',
            checked     : true,
            handler: function() {
                if (Ext.getCmp('ClientWorkingSetWorking').getValue()) {
                    var grid = Ext.getCmp('ClientsGrid');
                    ClientsGrid_Active = 1;
                    grid.store.setProxy( ActiveClientsGridProxy );   // меняем ссылку для забора только АКТИВНЫХ объектов
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
            name        : 'ClientWorkingSet',
            inputValue  : 'ClientWorkingSetArchive',
            id          : 'ClientWorkingSetArchive',
            padding     : '0 0 0 0',
            handler: function() {
                if (Ext.getCmp('ClientWorkingSetArchive').getValue()) {
                    var grid = Ext.getCmp('ClientsGrid');
                    ClientsGrid_Active = 0;
                    grid.store.setProxy( ArchivedClientsGridProxy ); // меняем ссылку для забора только АРХИВНЫХ объектов
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
                var grid = Ext.getCmp('ClientsGrid');
                var store= Ext.data.StoreManager.lookup('ClientsGridStore');
                var selectedRecord = grid.getSelectionModel().getSelection()[0];
                var ClientWindow   = Ext.widget('ClientWindow');
                var ClientId         = selectedRecord.data.id;


                var ClientForm = Ext.getCmp('ClientForm');
                //Ext.apply(Ext.getCmp('ObjectPhotosUploadBtn'), {SelectedObjectId : ClientId}); // вставляем id объекта в свойство кнопки-загрузчика
                ClientWindow.setTitle(Words_EditClientTitle + ' №' + ClientId); // заголовок - редактирование
                ClientWindow.show();            // открываем окно
                ClientForm.getForm().reset();   // сбрасываем предыдущую форму???
                ClientForm.getForm().load({     // загружаем данные в форму
                    waitMsg :'Загружаются данные клиента № ' + ClientId,
                    url     : 'Super.php',
                    method  : 'GET',
                    params  : {
                        id      : ClientId,
                        Action  : 'OpenClient'},
                    success: function(response, options) {

                        //var CreatePasswordBtn = Ext.ComponentQuery.query('#CreatePasswordBtn')[0];
                        //CreatePasswordBtn.setVisible(true);     // открываем кнопку создать пароль
                    }
                });


            },
            // You can also pass 'body' if you don't want click on the header or
            // docked elements
            element: 'body'
        },

    }

});
