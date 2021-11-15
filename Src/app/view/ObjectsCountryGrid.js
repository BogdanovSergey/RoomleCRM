Ext.define('crm.view.ObjectsCountryGrid', {
    extend      : 'Ext.grid.Panel',
    alias       : 'widget.ObjectsCountryGrid',
    title       : 'Объекты загородной недвижимости',
    stripeRows  : false,
    id          : "ObjectsCountryGrid",
    flex        : 1,
    // После создания грида (см. listeners: afterrender) перепроверяем права для коррекции вида // InitUserAccessRights();
    initComponent: function() {
        OpenedObjectsGrid = 'country';
        FilterOwnerUserSelect_RealtyType = 'country'; // для кнопки-фильтра
        GlobVars.OpenedRealtyType = 'country';
        Ext.apply(this, {
            store   : Ext.data.StoreManager.lookup('ObjectsCountryGridStore'),//'ObjectsCountryGridStore',
            id      : "ObjectsCountryGrid",
            viewConfig: {
                getRowClass: function(record, index, rowParams) {
                    // делаем подсветку строки классами определенными в index.html
                    if(record.get('Color')      == 'LightRed')    { return 'ObjectsGridRowColor_LightRed'; }
                    else if(record.get('Color') == 'LightYellow') { return 'ObjectsGridRowColor_LightYellow'; }
                    else if(record.get('Color') == 'LightBrown')  { return 'ObjectsGridRowColor_LightBrown'; }
                }
            },
            tbar: [
                {
                    text    : 'Создать новый объект',
                    iconCls : 'AddCls',
                    disabled: true,
                    itemId  : 'Btn_CreateObject',
                    handler : function() {
                        var ObjectCountryWindow = Ext.widget('ObjectCountryWindow');
                        //WhileOpeningTheObject();

                        ObjectCountryWindow.setTitle(Words_CreateCountryObjectTitle);
                        //Ext.getCmp('ObjectTabs').down('#ObjectPhotosTab').setDisabled(true); // новому объекту сначала нужно сохранить характеристики, затем открыть возм загрузки фото
                        //Ext.getCmp('SquareLiving').setFieldLabel('Жилая'); // приводим в порядок форму (могли быть оставлны изменения при редактировании объектов)
                        //Ext.getCmp('RoomsSellRow').setVisible(false);
                        //Ext.apply(Ext.getCmp('RoomsSell'), {allowBlank: true}, {});
                        ObjectCountryWindow.show();
                    }
                },

                '-',
                '->',
                '-',

                'Фильтр объектов:',

                Ext.create('Ext.form.Panel', {
                        border  : false,
                        itemId : 'lalala',
                        bodyStyle: {
                            background: 'inherit'
                        },
                        layout: 'column',
                        vertical : false,
                        items: [
                            {
                                xtype           : 'radiogroup',
                                fieldLabel      : '',
                                labelSeparator  : '',
                                //defaultType     : 'radiofield',
                                labelWidth  : 0,
                                width       : 160,
                                //columns     : 2,
                                items: [{
                                    boxLabel    : 'Рабочие',
                                    name        : 'ObjWorkingSet',
                                    id          : 'ObjWorkingSetWorking',
                                    checked     : true,
                                    handler: function() {
                                        if (Ext.getCmp('ObjWorkingSetWorking').getValue()) {
                                            var ObjGrid = Ext.getCmp('ObjectsCountryGrid');
                                            CountryObjectsGrid_Active = 1;
                                            ObjGrid.getStore().proxy.url = BuildGridUrlString(MainAjaxDriver, CountryObjectsGrid_Action, CountryObjectsGrid_Active, CountryObjectsGrid_OnlyUserId);
                                            ObjGrid.getStore().load();                            // перегружаем грид
                                            ObjGrid.columns[ findColumnIndex(ObjGrid.columns, 'AddedDate') ].setVisible(true); // показываем колонку AddedDate
                                            ObjGrid.columns[ findColumnIndex(ObjGrid.columns, 'ArchivedDate') ].setVisible(false); // прячем колонку ArchivedDate
                                            ObjGrid.columns[ findColumnIndex(ObjGrid.columns, 'DeleteColumn') ].setVisible(true); // показываем колонку DeleteColumn
                                            ObjGrid.columns[ findColumnIndex(ObjGrid.columns, 'RestoreColumn') ].setVisible(false); // прячем колонку RestoreColumn

                                            // обновляем содержание кнопки
                                            FilterOwnerUserSelect_ActiveObjects    = 1; //
                                            var SelectBtn = Ext.getCmp('FilterOwnerUserId');
                                            SelectBtn.getStore().proxy.url = BuildFilterOwnerUserSelectUrlString(MainAjaxDriver, FilterOwnerUserSelect_Action, FilterOwnerUserSelect_ActiveObjects, FilterOwnerUserSelect_GetAgents, FilterOwnerUserSelect_OnlyFio, FilterOwnerUserSelect_WithSumm, FilterOwnerUserSelect_RealtyType);
                                            //SelectBtn.reset();
                                            SelectBtn.getStore().load();
                                        }
                                        return;
                                    }
                                }, {
                                    boxLabel    : 'Архивные',
                                    name        : 'ObjWorkingSet',
                                    id          : 'ObjWorkingSetArchive',
                                    handler: function() {
                                        if (Ext.getCmp('ObjWorkingSetArchive').getValue()) {
                                            var ObjGrid = Ext.getCmp('ObjectsCountryGrid');
                                            CountryObjectsGrid_Active = 0; // глоб параметр для грида
                                            ObjGrid.getStore().proxy.url = BuildGridUrlString(MainAjaxDriver, CountryObjectsGrid_Action, CountryObjectsGrid_Active, CountryObjectsGrid_OnlyUserId);
                                            ObjGrid.getStore().load();                            // перегружаем грид
                                            ObjGrid.columns[ findColumnIndex(ObjGrid.columns, 'AddedDate') ].setVisible(false);   // прячем колонку AddedDate
                                            ObjGrid.columns[ findColumnIndex(ObjGrid.columns, 'ArchivedDate') ].setVisible(true);    // показываем колонку ArchivedDate
                                            ObjGrid.columns[ findColumnIndex(ObjGrid.columns, 'DeleteColumn') ].setVisible(false);  // прячем колонку DeleteColumn
                                            ObjGrid.columns[ findColumnIndex(ObjGrid.columns, 'RestoreColumn') ].setVisible(true);   // показываем колонку RestoreColumn

                                            // обновляем содержание кнопки
                                            FilterOwnerUserSelect_ActiveObjects    = 0;
                                            var SelectBtn = Ext.getCmp('FilterOwnerUserId');
                                            SelectBtn.getStore().proxy.url = BuildFilterOwnerUserSelectUrlString(MainAjaxDriver, FilterOwnerUserSelect_Action, FilterOwnerUserSelect_ActiveObjects, FilterOwnerUserSelect_GetAgents,FilterOwnerUserSelect_OnlyFio,FilterOwnerUserSelect_WithSumm,FilterOwnerUserSelect_RealtyType);
                                            SelectBtn.getStore().load();
                                        }
                                        return;
                                    }
                                }]
                            },
                            {
                                emptyText   : 'Агент',
                                fieldLabel  : ' ',
                                labelSeparator  : ' ',
                                xtype       : 'combo',
                                id          : 'FilterOwnerUserId',
                                itemId      : 'FilterOwnerUserId',
                                name        : 'FilterOwnerUserId',
                                triggerAction:  'all',
                                forceSelection: true,
                                editable    : false,
                                allowBlank  : true,
                                //queryParam  : 'GetAgents',
                                mode        : 'remote',
                                displayField:'VarName',
                                valueField  : 'id',
                                width       : 250,
                                store       : Ext.create('Ext.data.Store',{
                                    fields: [
                                        {name: 'id'},
                                        {name: 'VarName'}
                                    ],
                                    autoLoad: true,
                                    proxy: {
                                        type: 'ajax',
                                        url : BuildFilterOwnerUserSelectUrlString(MainAjaxDriver, FilterOwnerUserSelect_Action, FilterOwnerUserSelect_ActiveObjects, FilterOwnerUserSelect_GetAgents, FilterOwnerUserSelect_OnlyFio, FilterOwnerUserSelect_WithSumm, FilterOwnerUserSelect_RealtyType),
                                        reader: {
                                            type: 'json'
                                        }
                                    }
                                }),
                                listeners: {
                                    select: {
                                        fn:function(combo, value) {
                                            var SelectedId = combo.getValue();
                                            CountryObjectsGrid_OnlyUserId = SelectedId; // обновляем глоб переменные для построения url
                                            // обновляем url списка фоток
                                            var grid = Ext.getCmp('ObjectsCountryGrid');
                                            grid.getStore().proxy.url = BuildGridUrlString(MainAjaxDriver, CountryObjectsGrid_Action, CountryObjectsGrid_Active, CountryObjectsGrid_OnlyUserId);
                                            grid.getStore().load();
                                        }
                                    }
                                }
                            },
                            Ext.create('Ext.Button', {
                                //iconCls : 'ClrFilterCls',
                                text    : 'Сброс фильтра',
                                handler : function() {
                                    CountryObjectsGrid_OnlyUserId = '';
                                    var grid = Ext.getCmp('ObjectsCountryGrid');

                                    // сброс кнопки-фильтра
                                    Ext.getCmp('FilterOwnerUserId').reset();

                                    if(CountryObjectsGrid_Active == 1) {
                                        // грид обновим вручную (т.к. уже нажата радио кнопка)
                                        var url  = BuildGridUrlString(MainAjaxDriver, CountryObjectsGrid_Action, CountryObjectsGrid_Active, CountryObjectsGrid_OnlyUserId);
                                        grid.getStore().proxy.url = url;
                                        grid.getStore().load();
                                    } else {
                                        // грид обновится сам, по собитию изменения кнопки
                                        this.up('form').getForm().reset();
                                    }
                                    // сбрасываем
                                    //alert(s);
                                    //grid.getStore().proxy.url = MainAjaxDriver + '?' + ActiveCountryObjectsGridProxyParams;
                                    //crm.view.Mail.MailListWindow.create({});
                                    //Ext.widget("UsersListWindow").create();

                                    //this.up('form').getForm().reset(); // если радио переключено, срабатывает reload формы!
                                }
                            })
                        ]
                    }
                )
            ],
            columns : [
                {
                    text     : '№',
                    dataIndex: 'id',
                    width    :   40
                },
                {   dataIndex: 'Color',
                    hidden   : true
                },
                {   text     : 'Добавлено',
                    dataIndex: 'AddedDate',
                    width    : 80
                },
                {   text     : 'Удалено',
                    dataIndex: 'ArchivedDate',
                    width    : 80,
                    hidden   : true
                },
                {
                    text     : 'Фото',
                    dataIndex: 'ImagesCount',
                    width    : 30,
                    renderer: function(value) {
                        if(value > 0) {
                            return Ext.String.format('<img src="icons/photos.png" width="12" title="Загружено фотографий: {1}">', value, value);
                        }
                    }
                },
                {
                    text     : 'Тип',
                    dataIndex: 'ObjectTypeName',
                    width:   80,
                    flex     : 1
                },
                {
                    text     : 'Шоссе',
                    dataIndex: 'DirectionName',
                    width:   90,
                    flex     : 1
                },
                {
                    text     : 'Район',
                    dataIndex: 'Raion',
                    width:   100,
                    flex     : 1
                },
                {
                    text     : 'Нас.пункт',
                    dataIndex: 'City',
                    flex     : 1
                },
                {
                    text     : 'Улица',
                    dataIndex: 'Street',
                    flex     : 1
                },
                {
                    text     : 'км',
                    dataIndex: 'Distance',
                    width:   40
                },
                {
                    text     : 'сот',
                    dataIndex: 'LandSquare',
                    width:   40
                },
                {
                    text     : 'Дом м2',
                    dataIndex: 'SquareLiving',
                    width:   50
                },
                {
                    text     : 'Цена',
                    dataIndex: 'Price'
                },
                {   text     : 'Сайт агентства',
                    width    : 20,
                    dataIndex: 'TrfAnSiteFree',
                    itemId   : 'TrfAnSiteFree',
                    xtype    : 'checkcolumn',
                    menuDisabled: true,
                    disabled : false,
                    hidden   : true,
                    editor: {
                        xtype   : 'checkbox',
                        cls     : 'x-grid-checkheader-editor' },
                    listeners: {
                        checkchange: function (column, recordIndex, checked) {
                            console.log('checkchange event');
                            TrfCheckchangeEvent('ObjectsCountryGrid', 'TrfAnSiteFree', recordIndex, checked);
                        }
                    },
                    renderer: function(value, metaData, record, rowIndex, colIndex, store, view) {
                        return TrfRenderer('ObjectsCountryGrid', value, rowIndex);
                    }
                },
                {   text     : 'Winner',        width   : 20,
                    dataIndex: 'TrfWinner',  xtype   : 'checkcolumn',
                    itemId   : 'TrfWinner',
                    menuDisabled: true,
                    disabled : false,
                    hidden   : true,
                    editor: {
                        xtype   : 'checkbox',
                        cls     : 'x-grid-checkheader-editor' },
                    listeners: {
                        checkchange: function (column, recordIndex, checked) {
                            console.log('checkchange event');
                            TrfCheckchangeEvent('ObjectsCountryGrid', 'TrfWinner', recordIndex, checked);
                        }
                    },
                    renderer: function(value, metaData, record, rowIndex, colIndex, store, view) {
                        return TrfRenderer('ObjectsCountryGrid', value, rowIndex);
                    }
                },
                {   text     : 'Циан',          width   : 20,
                    dataIndex: 'TrfCian',    xtype   : 'checkcolumn',
                    menuDisabled: true,
                    disabled : false,
                    hidden   : true,
                    itemId   : 'TrfCian',
                    editor: {
                        xtype   : 'checkbox',
                        cls     : 'x-grid-checkheader-editor' },
                    listeners: {
                        checkchange: function (column, recordIndex, checked) {
                            console.log('checkchange event');
                            TrfCheckchangeEvent('ObjectsCountryGrid', 'TrfCian', recordIndex, checked);
                        }
                    },
                    renderer: function(value, metaData, record, rowIndex, colIndex, store, view) {
                        return TrfRenderer('ObjectsCountryGrid', value, rowIndex);
                    }
                },
                {   text     : 'ЦианПремиум',    width   : 60,
                    dataIndex: 'TrfCianPremium', xtype   : 'checkcolumn',
                    menuDisabled: true,
                    disabled : false,
                    hidden   : true,
                    itemId   : 'TrfCianPremium',
                    editor: {
                        xtype   : 'checkbox',
                        cls     : 'x-grid-checkheader-editor' },
                    listeners: {
                        checkchange: function (column, recordIndex, checked) {
                            console.log('checkchange event');
                            TrfCheckchangeEvent('ObjectsCountryGrid', 'TrfCianPremium', recordIndex, checked);
                            //Ext.Msg.alert(  'Установлен ЦИАН-премиум статус для объекта №' + rec.get('id'),
                            //   'Вы установили ЦИАН-премиум статус для объекта:<br><br><b>' + rec.get('City') + ' ' + rec.get('Street') +'</b> - ' + rec.get('Agent') + '<br><br><img src="icons/money.png"> За данную операцию взимается дополнительная плата. <br>Проконтроллируйте выход объявления на портале.');
                        }
                    },
                    renderer: function(value, metaData, record, rowIndex, colIndex, store, view) {
                        return TrfRenderer('ObjectsCountryGrid', value, rowIndex);
                    }
                },

                {   text     : 'Авито',         width   : 20,
                    dataIndex: 'TrfAvito',   xtype   : 'checkcolumn',
                    menuDisabled: true,
                    disabled : false,
                    hidden   : true,
                    itemId   : 'TrfAvito',
                    editor: {
                        xtype   : 'checkbox',
                        cls     : 'x-grid-checkheader-editor' },
                    listeners: {
                        checkchange: function (column, recordIndex, checked) {
                            console.log('checkchange event');
                            TrfCheckchangeEvent('ObjectsCountryGrid', 'TrfAvito', recordIndex, checked);
                        }
                    },
                    renderer: function(value, metaData, record, rowIndex, colIndex, store, view) {
                        return TrfRenderer('ObjectsCountryGrid', value, rowIndex);
                    }
                },
                {   text     : 'Навигатор',         width   : 20,
                    dataIndex: 'TrfNavigatorFree',   xtype   : 'checkcolumn',
                    menuDisabled: true,
                    disabled : false,
                    hidden   : true,
                    itemId   : 'TrfNavigatorFree',
                    editor: {
                        xtype   : 'checkbox',
                        cls     : 'x-grid-checkheader-editor' },
                    listeners: {
                        checkchange: function (column, recordIndex, checked) {
                            console.log('checkchange event');
                            TrfCheckchangeEvent('ObjectsCountryGrid', 'TrfNavigatorFree', recordIndex, checked);
                        }
                    },
                    renderer: function(value, metaData, record, rowIndex, colIndex, store, view) {
                        return TrfRenderer('ObjectsCountryGrid', value, rowIndex);
                    }
                },
                {   text     : 'РБК',         width   : 20,
                    dataIndex: 'TrfRbcFree',   xtype   : 'checkcolumn',
                    menuDisabled: true,
                    disabled : false,
                    hidden   : true,
                    itemId   : 'TrfRbcFree',
                    editor: {
                        xtype   : 'checkbox',
                        cls     : 'x-grid-checkheader-editor' },
                    listeners: {
                        checkchange: function (column, recordIndex, checked) {
                            console.log('checkchange event');
                            TrfCheckchangeEvent('ObjectsCountryGrid', 'TrfRbcFree', recordIndex, checked);
                        }
                    },
                    renderer: function(value, metaData, record, rowIndex, colIndex, store, view) {
                        return TrfRenderer('ObjectsCountryGrid', value, rowIndex);
                    }
                },
                {   text     : 'Afy',         width   : 20,
                    dataIndex: 'TrfAfy',   xtype   : 'checkcolumn',
                    menuDisabled: true,
                    disabled : false,
                    hidden   : true,
                    itemId   : 'TrfAfy',
                    editor: {
                        xtype   : 'checkbox',
                        cls     : 'x-grid-checkheader-editor' },
                    listeners: {
                        checkchange: function (column, recordIndex, checked) {
                            console.log('checkchange event');
                            TrfCheckchangeEvent('ObjectsCountryGrid', 'TrfAfy', recordIndex, checked);
                        }
                    },
                    renderer: function(value, metaData, record, rowIndex, colIndex, store, view) {
                        return TrfRenderer('ObjectsCountryGrid', value, rowIndex);
                    }
                },
                {   text     : 'Yandex',      width   : 20,
                    dataIndex: 'TrfYandex',   xtype   : 'checkcolumn',
                    menuDisabled: true,
                    disabled : false,
                    hidden   : true,
                    itemId   : 'TrfYandex',
                    editor: {
                        xtype   : 'checkbox',
                        cls     : 'x-grid-checkheader-editor' },
                    listeners: {
                        checkchange: function (column, recordIndex, checked) {
                            console.log('checkchange event');
                            TrfCheckchangeEvent('ObjectsCountryGrid', 'TrfYandex', recordIndex, checked);
                        }
                    },
                    renderer: function(value, metaData, record, rowIndex, colIndex, store, view) {
                        return TrfRenderer('ObjectsCountryGrid', value, rowIndex);
                    }
                },
                {
                    text     : 'Затраты на рекламу',
                    dataIndex: 'AdCosts',
                    itemId   : 'AdCosts',
                    menuDisabled: true,
                    hidden   : false,
                    width    : 50
                },
                {
                    text     : 'Агент',
                    dataIndex: 'Agent',
                    flex     : 1
                },
                {
                    hidden   : true,
                    dataIndex: 'OwnerUserId'
                },
                {   xtype   : 'actioncolumn',
                    text    : 'В архив',
                    menuDisabled: true,
                    width   : 30,
                    dataIndex: 'DeleteColumn',
                    items   : [{
                        tooltip : 'В архив',
                        getClass: function (value, meta, record, rowIndex, colIndex) {
                            if(CheckUserAccessRule('Objects-All-Manage')) {
                                var cls = 'DeleteCls';
                            } else if(CheckUserAccessRule('Objects-My-Manage') && record.get('OwnerUserId')== GlobVars.CurrentUser.id ) {
                                var cls = 'DeleteCls';
                            } else {
                                var cls = 'InvisibleItem';  //ничего не показываем
                            }
                            return cls;
                        },
                        handler:function (grid, rowIndex, colIndex) {
                            var rec = grid.getStore().getAt(rowIndex);
                            ArchivateObjectById( rowIndex, rec.get('id'), 'ObjectsCountryGridStore', rec.get('Street') );
                        }
                    }]
                },
                {   xtype       : 'actioncolumn',
                    width       : 30,
                    text        : 'Восстановить',
                    dataIndex   : 'RestoreColumn',
                    hidden      : true,
                    menuDisabled: true,
                    items       : [{
                        tooltip : 'Восстановить',
                        getClass: function (value, meta, record, rowIndex, colIndex) {
                            if(CheckUserAccessRule('Objects-All-Manage')) {
                                var cls = 'RestoreCls';
                            } else if(CheckUserAccessRule('Objects-My-Manage') && record.get('OwnerUserId')== GlobVars.CurrentUser.id ) {
                                var cls = 'RestoreCls';
                            } else {
                                var cls = 'InvisibleItem';  //ничего не показываем
                            }
                            return cls;
                        },
                        handler:function (grid, rowIndex, colIndex) {
                            var rec = grid.getStore().getAt(rowIndex);
                            RestoreObjectById( rowIndex, rec.get('id'), 'ObjectsCountryGridStore', rec.get('Street') );
                        }
                    }]
                }

            ],
            dockedItems : [
                {
                    xtype       : 'toolbar',
                    dock        : 'bottom',
                    items: [
                        {
                            text    : '',
                            iconCls : 'x-tbar-loading',
                            hidden  : false,
                            handler : function() {
                                Ext.getCmp('ObjectsCountryGrid').getStore().load();
                            }
                        },
                        {
                            text    : 'Excel',
                            iconCls : 'ExportToExcelCls',
                            hidden  : false,
                            handler : function() {
                                if(typeof Ext.data.StoreManager.lookup('ObjectsCountryGridStore').sorters.items[0] !== "undefined") {
                                    var SortProp = Ext.data.StoreManager.lookup('ObjectsCountryGridStore').sorters.items[0].property;
                                }//#COLUMNSORTING
                                if(typeof Ext.data.StoreManager.lookup('ObjectsCountryGridStore').sorters.items[0] !== "undefined") {
                                    var SortDir  = Ext.data.StoreManager.lookup('ObjectsCountryGridStore').sorters.items[0].direction;
                                }
                                var DownloadUrl = 'Super.php?' + Ext.data.StoreManager.lookup('ObjectsCountryGridStore').proxy.Params + '&DownloadType=xls';
                                var DownloadUrl = BuildObjectExportBtnUrlString(MainAjaxDriver, CountryObjectsGrid_Action, CountryObjectsGrid_Active, CountryObjectsGrid_OnlyUserId,
                                    'xls',
                                    SortProp,
                                    SortDir);
                                console.log( DownloadUrl );
                                document.location = DownloadUrl;
                            }
                        },
                        { xtype: 'tbfill' },
                        {
                            xtype   : 'tbtext',
                            //id      : 'ObjectsCountryCountLabel',
                            itemId  : 'ObjectsCountryCountLabel',
                            text    : 'Объекты не загружены', // сменится на "Всего объектов: ххх" через контроллер
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

    /**/

    listeners: {
        afterrender: {
            fn: function() {
                InitUserAccessRights();
            }
        },
        dblclick : {
            fn: function() {                    // Открытие объекта при двойном клике
                var selectedRecord  = Ext.getCmp('ObjectsCountryGrid').getSelectionModel().getSelection()[0];
                var ObjectCountryTabsName = 'ObjectCountryTabs';
                var ObjectWindow = Ext.widget('ObjectCountryWindow');
                var form    = 'ObjectCountryForm';
                var tab     = 'ObjectTabs';

                var ObjectForm = Ext.getCmp(form);
                var ObjectTabs = Ext.getCmp(tab);


                var ObjectAdditionsFormObj = Ext.getCmp('ObjectCountryAdditionsForm');
                ObjectsGridDblclick(ObjectCountryTabsName, ObjectWindow, ObjectForm, ObjectAdditionsFormObj, selectedRecord );
                //ObjectsCountryGridDblclick(ObjectCountryTabsName, ObjectWindow, ObjectForm, ObjectAdditionsFormObj, selectedRecord );
            },
            // You can also pass 'body' if you don't want click on the header or
            // docked elements
            element: 'body'
        }
    }
/*
    stateId     : 'CityObjectsCountryGridState',
    stateful: true, // state should be preserved
    stateEvents: ['columnresize', 'columnmove', 'show', 'hide' ]*/
});
