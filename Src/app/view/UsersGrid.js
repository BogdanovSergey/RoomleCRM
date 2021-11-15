// главный список пользоавтелей
Ext.define('crm.view.UsersGrid', {
    extend      : 'Ext.grid.Panel',
    id          : "UsersGrid",
    alias       : 'widget.UsersGrid',

    stripeRows  : false,
    /*stateId     : 'UsersGridState',
    stateful    : true,
    stateEvents: ['columnresize', 'columnmove', 'show', 'hide'],*/
    initComponent: function() {
        Ext.apply(this, {
            store : 'UsersGridStore',
            columns : [
            {
                text     : '№',
                dataIndex: 'id',
                width:   20
            },
            {   text     : 'Добавлен',
                dataIndex: 'AddedDate',
                width    : 50
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
                flex     : 1
            },
            {
                text     : 'Имя Отчество',
                dataIndex: 'FirstName',
                //width:   60,
                flex     : 1
            },
            {
                text     : 'Должность',
                //sortable : false,
                dataIndex: 'Position'
            },
            {
                text     : 'Отдел',
                //sortable : false,
                dataIndex: 'Group'
            },
            {   // поле для удобной загрузки прав в SetRightsStoragesExtraParams()
                hidden   : true,
                dataIndex: 'PositionId'
            },
            {   // поле для удобной загрузки прав в SetRightsStoragesExtraParams()
                hidden   : true,
                dataIndex: 'GroupId'
            },
            {
                text     : 'Статус',
                //sortable : false,
                dataIndex: 'Status',
                width    :   50
            },
            {
                text     : 'Текущий счет',
                dataIndex: 'CurrentSumm',
                width    :   50
            },
            {
                text     : 'Email',
                dataIndex: 'Email'
            },
            {
                text     : 'Основной номер',
                dataIndex: 'MobilePhone',
                flex     : 1
            },
            {
                text     : 'День рождения',
                dataIndex: 'Birthday'
            },
            {
                text     : 'Последний вход',
                dataIndex: 'LastEnter'
            },
            {
                text     : 'Альт. номер',
                dataIndex: 'MobilePhone1',
                hidden   : true,
                width:   30
            },
            {
                text     : 'Альт. номер',
                dataIndex: 'MobilePhone2',
                hidden   : true,
                width:   30
            },
            {   xtype       : 'actioncolumn',
                text        : 'Удалить',
                width       : 30,
                sortable    : false,
                dataIndex   : 'DeleteColumn',
                items       : [{
                        //icon:'icons/cross.png',
                        getClass: function (value, meta, record, rowIndex, colIndex) {
                            if((CheckUserAccessRule('Users-All-ReadEditDeleteRestore') || CheckUserAccessRule('Users-All-Delete')) &&
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
                            }
                            return cls;
                        },
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
                sortable    : false,
                hidden      : true,
                items       : [{
                    //icon:'icons/arrow_undo.png',
                    getClass: function (value, meta, record, rowIndex, colIndex) {
                        if((CheckUserAccessRule('Users-All-ReadEditDeleteRestore') || CheckUserAccessRule('Users-All-Restore'))&& !CheckUserAccessRule('Users-LimitByOwnGroup')) {
                            var cls = 'RestoreCls';
                        } else if((CheckUserAccessRule('Users-All-ReadEditDeleteRestore') || CheckUserAccessRule('Users-All-Restore')) && CheckUserAccessRule('Users-LimitByOwnGroup') && record.get('GroupId')== GlobVars.CurrentUser.GroupIdsArr[0] ) { // проверка только первой группы
                            // TODO сделать цикл на проверку массива отделов #USERGROUPARR
                            var cls = 'RestoreCls';
                        } else {
                            var cls = 'InvisibleItem';  //ничего не показываем
                        }
                        return cls;
                    },
                    handler:function (grid, rowIndex, colIndex) {
                        var rec = grid.getStore().getAt(rowIndex);
                        RestoreUserById( rowIndex, rec.get('id'), rec.get('LastName') );
                    }
                }]
            }
            ],
            dockedItems : [
                /*{
                    xtype       : 'pagingtoolbar',
                    store       : 'UsersGridStore',//Ext.data.StoreManager.lookup('UsersGridStore'),
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
                                Ext.getCmp('UsersGrid').getStore().load();
                            }
                        },
                        {
                            text    : 'Excel',
                            iconCls : 'ExportToExcelCls',
                            hidden  : false,
                            handler : function() {                                        // TODO сделать сортировку по неск столбцам
                                if(typeof Ext.data.StoreManager.lookup('UsersGridStore').sorters.items[0] !== "undefined") {
                                    var SortProp = Ext.data.StoreManager.lookup('UsersGridStore').sorters.items[0].property;
                                }
                                if(typeof Ext.data.StoreManager.lookup('UsersGridStore').sorters.items[0] !== "undefined") {
                                    var SortDir  = Ext.data.StoreManager.lookup('UsersGridStore').sorters.items[0].direction;
                                }
                                var DownloadUrl = BuildUserExportBtnUrlString(MainAjaxDriver, UsersGrid_Action, UsersGrid_Active, UsersGrid_OnlyUserId,
                                    'xls',
                                    SortProp,
                                    SortDir);
                                document.location = DownloadUrl;
                            }
                        },

                        { xtype: 'tbfill' },
                        {
                            xtype   : 'tbtext',
                            itemId  : 'UsersListCountLabel',
                            text    : 'Сотрудники не загружены', // сменится на "Всего ___: ххх" через контроллер
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
        text    : 'Создать нового сотрудника',
        iconCls : 'AddCls',
        disabled: true,
        itemId  : 'Btn_CreateUser',
        handler : function() {
            var UserWindow = Ext.widget('UserWindow');
            WhileOpeningTheUserForm();
            UserWindow.setTitle(Words_CreateUserTitle);

            //Ext.getCmp('ObjectTabs').down('#ObjectPhotosTab').setDisabled(true); // новому объекту сначала нужно сохранить характеристики, затем открыть возм загрузки фото
            //Ext.getCmp('SquareLiving').setFieldLabel('Жилая'); // приводим в порядок форму (могли быть оставлны изменения при редактировании объектов)
            //Ext.getCmp('RoomsSellRow').setVisible(false);
            //Ext.apply(Ext.getCmp('RoomsSell'), {allowBlank: true}, {});



            UserWindow.show();
        }
    },
    '-',
    {
        xtype           : 'radiogroup',
        fieldLabel      : ' ',
        labelSeparator  : ' ',
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
                    UsersGrid_Active = 1;
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
            padding     : '0 0 0 0',
            handler: function() {
                if (Ext.getCmp('UsersWorkingSetArchive').getValue()) {
                    var grid = Ext.getCmp('UsersGrid');
                    UsersGrid_Active = 0;
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
                var grid = Ext.getCmp('UsersGrid');
                var store= Ext.data.StoreManager.lookup('UsersGridStore');
                var selectedRecord = grid.getSelectionModel().getSelection()[0];
                var UserWindow      = Ext.widget('UserWindow');
                var UserId          = selectedRecord.data.id;
                // после изменения нижеследующих полей в форме, грид держит старые данные выбранной строки (selected row).
                // берем данные через грид
                var PositionId      = store.getAt( grid.getSelectionModel().getSelection()[0].index ).data.PositionId;
                var GroupId      = store.getAt( grid.getSelectionModel().getSelection()[0].index ).data.GroupId;

                // проверки на права открытия и управления
                if(
                    // пользователь может управлять учетками только своего отдела // TODO сделать цикл на проверку массива отделов #USERGROUPARR
                    ((CheckUserAccessRule('Users-All-ReadEditDeleteRestore') || CheckUserAccessRule('Users-All-Edit')) && CheckUserAccessRule('Users-LimitByOwnGroup') && GroupId == GlobVars.CurrentUser.GroupIdsArr[0]) ||
                    // стоит право на все, отделом не ограничивается
                    ((CheckUserAccessRule('Users-All-ReadEditDeleteRestore') || CheckUserAccessRule('Users-All-Edit')) && !CheckUserAccessRule('Users-LimitByOwnGroup') )
                ) {
                        //WhileOpeningTheUserForm(UserId, selectedRecord.data.PositionId, selectedRecord.data.GroupId); // selectedRecord не обновляется в измененном гриде, даже после reload()
                        WhileOpeningTheUserForm(UserId, PositionId, GroupId);
                        var UserForm = Ext.getCmp('UserForm');
                        //Ext.apply(Ext.getCmp('ObjectPhotosUploadBtn'), {SelectedObjectId : UserId}); // вставляем id объекта в свойство кнопки-загрузчика
                        UserWindow.setTitle(Words_EditUserTitle + ' №' + UserId); // заголовок - редактирование
                        UserWindow.show();            // открываем окно
                        UserForm.getForm().reset();   // сбрасываем предыдущую форму
                        UserForm.getForm().load({     // загружаем данные в форму
                            waitMsg :'Загружаются данные сотрудника № ' + UserId,
                            url     : 'Super.php',
                            method  : 'GET',
                            params  : {
                                id      : UserId,
                                Action  : 'OpenUser'},
                            success: function(response, options) {
                                // подготавливаем поля для пароля
                                var GenBtn = Ext.ComponentQuery.query('#GeneratePasswordBtn')[0];
                                var ChkBtn = Ext.ComponentQuery.query('#PassChkbx')[0];
                                var ClosedPassField = Ext.ComponentQuery.query('#ClosedPassword1')[0];
                                var OpenedPassField = Ext.ComponentQuery.query('#OpenedPassword1')[0];
                                var OldPassField    = Ext.ComponentQuery.query('#OldPassword')[0];
                                OpenedPassField.setVisible(false);
                                ClosedPassField.setVisible(false);
                                GenBtn.setVisible(false);
                                ChkBtn.setVisible(false);
                                OldPassField.setVisible(true);

                                var CreatePasswordBtn = Ext.ComponentQuery.query('#CreatePasswordBtn')[0];
                                CreatePasswordBtn.setVisible(true);     // открываем кнопку создать пароль
                            }
                        });

                } else {
                    Ext.Msg.show({
                        title   : ' ',
                        msg     : Words_NoRules,
                        buttons : Ext.Msg.OK,
                        icon    : Ext.Msg.ERROR
                    });

                }

            },
            // You can also pass 'body' if you don't want click on the header or
            // docked elements
            element: 'body'
        },

    }

});
