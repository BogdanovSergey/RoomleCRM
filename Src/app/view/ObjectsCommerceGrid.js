Ext.define('crm.view.ObjectsCommerceGrid', {
    extend      : 'Ext.grid.Panel',
    alias       : 'widget.ObjectsCommerceGrid',
    id          : "ObjectsCommerceGrid",
    flex        : 1,
    title       : 'Объекты коммерческой недвижимости',
    stripeRows  : false,
    initComponent: function() {
        OpenedObjectsGrid = 'commerce';
        FilterOwnerUserSelect_RealtyType = 'commerce';
        GlobVars.OpenedRealtyType = 'commerce';
        // настраиваем кнопку-фильтр
        Ext.apply(this, {
            id      : "ObjectsCommerceGrid",
            store   : 'ObjectsCommerceGridStore',
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
                        var ObjectCommerceWindow = Ext.widget('ObjectCommerceWindow');
                        //var ObjectTabs = Ext.widget('ObjectCommerceTabs');
                        //    WhileOpeningTheObject();
                        ObjectCommerceWindow.setTitle(Words_CreateCommerceObjectTitle);
                        ObjectCommerceWindow.show();

                        //    ObjectTabs.down('#ObjectPhotosTab').setDisabled(true); // новому объекту сначала нужно сохранить характеристики, затем открыть возм загрузки фото
                        //Ext.getCmp('SquareLiving').setFieldLabel('Жилая'); // приводим в порядок форму (могли быть оставлны изменения при редактировании объектов)
                        //Ext.getCmp('RoomsSellRow').setVisible(false);
                        //Ext.apply(Ext.getCmp('RoomsSell'), {allowBlank: true}, {});

                        //Ext.data.StoreManager.lookup('ObjectCommerceForm.CurrencyStore')
                        //   Ext.ComponentQuery.query('#Currency')[0].setValue(  Ext.data.StoreManager.lookup('ObjectCommerceForm.CurrencyStore').getAt('0').get('RUB') );

                    }
                },
                //'-',
                {
                    xtype      : 'fieldcontainer',
                    fieldLabel: '',
                    labelSeparator : ' ',
                    //defaultType: 'textfield',
                    layout: 'hbox',
                    items: [
                        {
                            fieldLabel  : 'Открыть №',
                            hidden: true,
                            allowBlank  : true,
                            size        : 5,
                            name        : 'QuickId',
                            itemId      : 'QuickId',
                            vtype       : 'DigitsVtype',
                            xtype       : 'textfield',
                            width       : 120,
                            padding     : '0 0 0 10',
                            labelWidth  : 70,
                            inputAttrTpl: " data-qtip='Введите № объекта для быстрого открытия' ",
                            // listeners
                        }
                    ]
                },
                '-',
                '->',
                '-',

                'Фильтр объектов:',

                Ext.create('Ext.form.Panel', {
                    border: false,
                    //width: '800',
                    //minWidth:'800',
                    defaults: {
                        //anchor: '100%'
                    },
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
                                //inputValue  : 'ObjWorkingSetWorking',
                                //width       : 30,
                                //padding     :  '0 0 0 0',
                                checked     : true,
                                handler: function() {
                                    if (Ext.getCmp('ObjWorkingSetWorking').getValue()) {
                                        var ObjGrid = Ext.getCmp('ObjectsCommerceGrid');
                                        CommerceObjectsGrid_Active = 1;
                                        ObjGrid.getStore().proxy.url = BuildGridUrlString(MainAjaxDriver, CommerceObjectsGrid_Action, CommerceObjectsGrid_Active, CommerceObjectsGrid_OnlyUserId);
                                        //ObjGrid.store.setProxy( ActiveCommerceObjectsGridProxy );   // меняем ссылку для забора только АКТИВНЫХ объектов
                                        ObjGrid.getStore().load();                            // перегружаем грид
                                        ObjGrid.columns[ findColumnIndex(ObjGrid.columns, 'AddedDate') ].setVisible(true); // показываем колонку AddedDate
                                        ObjGrid.columns[ findColumnIndex(ObjGrid.columns, 'ArchivedDate') ].setVisible(false); // прячем колонку ArchivedDate
                                        ObjGrid.columns[ findColumnIndex(ObjGrid.columns, 'DeleteColumn') ].setVisible(true); // показываем колонку DeleteColumn
                                        ObjGrid.columns[ findColumnIndex(ObjGrid.columns, 'RestoreColumn') ].setVisible(false); // прячем колонку RestoreColumn

                                        // обновляем содержание кнопки
                                        FilterOwnerUserSelect_ActiveObjects    = 1;
                                        var SelectBtn = Ext.getCmp('FilterOwnerUserId');
                                        SelectBtn.getStore().proxy.url = BuildFilterOwnerUserSelectUrlString(MainAjaxDriver, FilterOwnerUserSelect_Action, FilterOwnerUserSelect_ActiveObjects, FilterOwnerUserSelect_GetAgents,FilterOwnerUserSelect_OnlyFio,FilterOwnerUserSelect_WithSumm,FilterOwnerUserSelect_RealtyType);
                                        //SelectBtn.reset();
                                        SelectBtn.getStore().load();
                                    }
                                    return;
                                }
                            }, {
                                boxLabel    : 'Архивные',
                                name        : 'ObjWorkingSet',
                                //inputValue  : 'ObjWorkingSetArchive',
                                id          : 'ObjWorkingSetArchive',
                                //padding     :  '0 0 0 0',hidden:true,
                                handler: function() {
                                    if (Ext.getCmp('ObjWorkingSetArchive').getValue()) {
                                        var ObjGrid = Ext.getCmp('ObjectsCommerceGrid');
                                        CommerceObjectsGrid_Active = 0; // глоб параметр для грида
                                        ObjGrid.getStore().proxy.url = BuildGridUrlString(MainAjaxDriver, CommerceObjectsGrid_Action, CommerceObjectsGrid_Active, CommerceObjectsGrid_OnlyUserId);

                                        //ObjGrid.getStore().setProxy( ArchivedCommerceObjectsGridProxy ); // меняем ссылку для забора только АРХИВНЫХ объектов
                                        ObjGrid.getStore().load();                            // перегружаем грид
                                        ObjGrid.columns[ findColumnIndex(ObjGrid.columns, 'AddedDate') ].setVisible(false);   // прячем колонку AddedDate
                                        ObjGrid.columns[ findColumnIndex(ObjGrid.columns, 'ArchivedDate') ].setVisible(true);    // показываем колонку ArchivedDate
                                        ObjGrid.columns[ findColumnIndex(ObjGrid.columns, 'DeleteColumn') ].setVisible(false);  // прячем колонку DeleteColumn
                                        ObjGrid.columns[ findColumnIndex(ObjGrid.columns, 'RestoreColumn') ].setVisible(true);   // показываем колонку RestoreColumn

                                        // обновляем содержание кнопки
                                        FilterOwnerUserSelect_ActiveObjects    = 0;
                                        var SelectBtn = Ext.getCmp('FilterOwnerUserId');
                             //console.log(SelectBtn.getStore().proxy.url);
                                        SelectBtn.getStore().proxy.url = BuildFilterOwnerUserSelectUrlString(MainAjaxDriver, FilterOwnerUserSelect_Action, FilterOwnerUserSelect_ActiveObjects, FilterOwnerUserSelect_GetAgents,FilterOwnerUserSelect_OnlyFio,FilterOwnerUserSelect_WithSumm,FilterOwnerUserSelect_RealtyType);
                                        //SelectBtn.reset();
                                        //console.log(SelectBtn.getStore().proxy.url);
                                        SelectBtn.getStore().load();// svdfas dfas dfas df ;'; '
                                        //Ext.ComponentQuery.query('#FilterOwnerUserId').getStore().load();
                                        //Ext.ComponentQuery.query('#FilterOwnerUserId')[0].store.load();
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
                            //padding     :  '0 0 0 32',
                            //store: Ext.data.StoreManager.lookup('FilterOwnerUserStore'),
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
                                        CommerceObjectsGrid_OnlyUserId = SelectedId; // обновляем глоб переменные для построения url
                                        // обновляем url списка фоток
                                        var grid = Ext.getCmp('ObjectsCommerceGrid');
                                        grid.getStore().proxy.url = BuildGridUrlString(MainAjaxDriver, CommerceObjectsGrid_Action, CommerceObjectsGrid_Active, CommerceObjectsGrid_OnlyUserId);
                                        grid.getStore().load();
                                    }
                                }
                            }
                        },
                        Ext.create('Ext.Button', {
                            //iconCls : 'ClrFilterCls',
                            text    : 'Сброс фильтра',
                            handler : function() {
                                CommerceObjectsGrid_OnlyUserId = '';
                                var grid = Ext.getCmp('ObjectsCommerceGrid');

                                // сброс кнопки-фильтра
                                Ext.getCmp('FilterOwnerUserId').reset();

                                if(CommerceObjectsGrid_Active == 1) {
                                    // грид обновим вручную (т.к. уже нажата радио кнопка)
                                    var url  = BuildGridUrlString(MainAjaxDriver, CommerceObjectsGrid_Action, CommerceObjectsGrid_Active, CommerceObjectsGrid_OnlyUserId);
                                    grid.getStore().proxy.url = url;
                                    grid.getStore().load();
                                } else {
                                    // грид обновится сам, по собитию изменения кнопки
                                    this.up('form').getForm().reset();
                                }
                                // сбрасываем
                                //alert(s);
                                //grid.getStore().proxy.url = MainAjaxDriver + '?' + ActiveCommerceObjectsGridProxyParams;
                                //crm.view.Mail.MailListWindow.create({});
                                //Ext.widget("UsersListWindow").create();

                                //this.up('form').getForm().reset(); // если радио переключено, срабатывает reload формы!
                            }
                        })
                    ]
                    }
                )

                //'-'////////
            ],
            columns : [
                {
                    text     : '№',
                    dataIndex: 'id',
                    width:   40
                },
                {   text     : 'Добавлено',
                    dataIndex: 'AddedDate',
                    width    : 80
                },
                {   dataIndex: 'Color',
                    hidden   : true
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
                            //<img src="icons/bullet_picture.png" width="10">
                            return Ext.String.format('<img src="icons/photos.png" width="12" title="Загружено фотографий: {1}">', value, value);
                        }
                    }
                },
                {
                    text        : 'Тип сделки',
                    dataIndex   : 'DealTypeName',
                    width       : 80,
                    flex        : 1
                },
                {
                    text        : 'Тип помещений',
                    dataIndex   : 'RoomTypeName',
                    width       : 80,
                    flex        : 1
                },
                {
                    text        : 'Назначение',
                    dataIndex   : 'CommerceObjectTypeName',
                    width       : 80,
                    flex        : 1
                },
                {
                    text        : 'Комнат',
                    dataIndex   : 'RoomsCount',
                    width       : 40,
                    hidden      : true
                },
                {
                    text     : 'Город',
                    dataIndex: 'City',
                    flex     : 1
                },
                {
                    text     : 'Метро',
                    dataIndex: 'Metro',
                    flex     : 1
                },
                {
                    text     : 'Улица',
                    dataIndex: 'Street',
                    flex     : 1
                },
                {
                    text     : 'Название',
                    dataIndex: 'ObjectBrandName',
                    flex     : 1
                },
                {
                    text     : 'Этажность',
                    dataIndex: 'Floors',
                    width    : 50,
                    hidden   : true
                },
                {
                    text     : 'Общая пл.',
                    dataIndex: 'SquareAll'
                },
                {
                    text     : 'Цена',
                    dataIndex: 'Price'
                },
                {
                    text     : 'Период оплаты',
                    dataIndex: 'PricePeriodName'
                },
                {
                    text     : 'Тип цены',
                    dataIndex: 'PriceTypeName'
                },
                {   text     : 'Сайт агентства',        width   : 20,
                    dataIndex: 'TrfAnSiteFree',  xtype   : 'checkcolumn',
                    itemId   : 'TrfAnSiteFree',
                    menuDisabled: true,
                    disabled : false,
                    hidden   : true,
                    editor: {
                        xtype   : 'checkbox',
                        cls     : 'x-grid-checkheader-editor' },
                    listeners: {
                        checkchange: function (column, recordIndex, checked) {
                            console.log('checkchange event');
                            TrfCheckchangeEvent('ObjectsCommerceGrid', 'TrfAnSiteFree', recordIndex, checked);
                        }
                    },
                    renderer: function(value, metaData, record, rowIndex, colIndex, store, view) {
                        return TrfRenderer('ObjectsCommerceGrid', value, rowIndex);
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
                            TrfCheckchangeEvent('ObjectsCommerceGrid', 'TrfWinner', recordIndex, checked);
                        }
                    },
                    renderer: function(value, metaData, record, rowIndex, colIndex, store, view) {
                        return TrfRenderer('ObjectsCommerceGrid', value, rowIndex);
                    }
                },
                {   text     : 'Циан',          width   : 20,
                    dataIndex: 'TrfCian',    xtype   : 'checkcolumn',
                    itemId   : 'TrfCian',
                    menuDisabled: true,
                    disabled : false,
                    hidden   : true,
                    editor: {
                        xtype   : 'checkbox',
                        cls     : 'x-grid-checkheader-editor' },
                    listeners: {
                        checkchange: function (column, recordIndex, checked) {
                            console.log('checkchange event');
                            TrfCheckchangeEvent('ObjectsCommerceGrid', 'TrfCian', recordIndex, checked);
                        }
                    },
                    renderer: function(value, metaData, record, rowIndex, colIndex, store, view) {
                        return TrfRenderer('ObjectsCommerceGrid', value, rowIndex);
                    }
                },
                {   text     : 'ЦианПремиум',    width   : 60,
                    dataIndex: 'TrfCianPremium', xtype   : 'checkcolumn',
                    itemId   : 'TrfCianPremium',
                    menuDisabled: true,
                    disabled : false,
                    hidden   : true,
                    editor: {
                        xtype   : 'checkbox',
                        cls     : 'x-grid-checkheader-editor' },
                    listeners: {
                        checkchange: function (column, recordIndex, checked) {
                            console.log('checkchange event');
                            TrfCheckchangeEvent('ObjectsCommerceGrid', 'TrfCianPremium', recordIndex, checked);
                            //Ext.Msg.alert(  'Установлен ЦИАН-премиум статус для объекта №' + rec.get('id'),
                        }   //                'Вы установили ЦИАН-премиум статус для объекта:<br><br><b>' + rec.get('City') + ' ' + rec.get('Street') +'</b> - ' + rec.get('Agent') + '<br><br><img src="icons/money.png"> За данную операцию взимается дополнительная плата. <br>Проконтроллируйте выход объявления на портале.');
                    },
                    renderer: function(value, metaData, record, rowIndex, colIndex, store, view) {
                        return TrfRenderer('ObjectsCommerceGrid', value, rowIndex);
                    }
                },
                {   text     : 'Авито',         width   : 20,
                    dataIndex: 'TrfAvito',   xtype   : 'checkcolumn',
                    itemId   : 'TrfAvito',
                    menuDisabled: true,
                    disabled : false,
                    hidden   : true,
                    editor: {
                        xtype   : 'checkbox',
                        cls     : 'x-grid-checkheader-editor' },
                    listeners: {
                        checkchange: function (column, recordIndex, checked) {
                            console.log('checkchange event');
                            TrfCheckchangeEvent('ObjectsCommerceGrid', 'TrfAvito', recordIndex, checked);
                        }
                    },
                    renderer: function(value, metaData, record, rowIndex, colIndex, store, view) {
                        return TrfRenderer('ObjectsCommerceGrid', value, rowIndex);
                    }
                },
                {   text     : 'Навигатор',         width   : 20,
                    dataIndex: 'TrfNavigatorFree',   xtype   : 'checkcolumn',
                    itemId   : 'TrfNavigatorFree',
                    menuDisabled: true,
                    disabled : false,
                    hidden   : true,
                    editor: {
                        xtype   : 'checkbox',
                        cls     : 'x-grid-checkheader-editor' },
                    listeners: {
                        checkchange: function (column, recordIndex, checked) {
                            console.log('checkchange event');
                            TrfCheckchangeEvent('ObjectsCommerceGrid', 'TrfNavigatorFree', recordIndex, checked);
                        }
                    },
                    renderer: function(value, metaData, record, rowIndex, colIndex, store, view) {
                        return TrfRenderer('ObjectsCommerceGrid', value, rowIndex);
                    }
                },
                {   text     : 'РБК',         width   : 20,
                    dataIndex: 'TrfRbcFree',   xtype   : 'checkcolumn',
                    itemId   : 'TrfRbcFree',
                    menuDisabled: true,
                    disabled : false,
                    hidden   : true,
                    editor: {
                        xtype   : 'checkbox',
                        cls     : 'x-grid-checkheader-editor' },
                    listeners: {
                        checkchange: function (column, recordIndex, checked) {
                            console.log('checkchange event');
                            TrfCheckchangeEvent('ObjectsCommerceGrid', 'TrfRbcFree', recordIndex, checked);
                        }
                    },
                    renderer: function(value, metaData, record, rowIndex, colIndex, store, view) {
                        return TrfRenderer('ObjectsCommerceGrid', value, rowIndex);
                    }
                },
                {   text     : 'Afy',         width   : 20,
                    dataIndex: 'TrfAfy',   xtype   : 'checkcolumn',
                    itemId   : 'TrfAfy',
                    menuDisabled: true,
                    disabled : false,
                    hidden   : true,
                    editor: {
                        xtype   : 'checkbox',
                        cls     : 'x-grid-checkheader-editor' },
                    listeners: {
                        checkchange: function (column, recordIndex, checked) {
                            console.log('checkchange event');
                            TrfCheckchangeEvent('ObjectsCommerceGrid', 'TrfAfy', recordIndex, checked);
                        }
                    },
                    renderer: function(value, metaData, record, rowIndex, colIndex, store, view) {
                        return TrfRenderer('ObjectsCommerceGrid', value, rowIndex);
                    }
                },
                {   text     : 'Yandex',         width   : 20,
                    dataIndex: 'TrfYandex',   xtype   : 'checkcolumn',
                    itemId   : 'TrfYandex',
                    menuDisabled: true,
                    disabled : false,
                    hidden   : true,
                    editor: {
                        xtype   : 'checkbox',
                        cls     : 'x-grid-checkheader-editor' },
                    listeners: {
                        checkchange: function (column, recordIndex, checked) {
                            console.log('checkchange event');
                            TrfCheckchangeEvent('ObjectsCommerceGrid','TrfYandex', recordIndex, checked);
                        }
                    },
                    renderer: function(value, metaData, record, rowIndex, colIndex, store, view) {
                        return TrfRenderer('ObjectsCommerceGrid', value, rowIndex);
                    }
                    /*handler: function(grid, rowIndex, colIndex) {
                     alert(rowIndex);
                     }*/
                },
                {
                    text     : 'Агент',
                    dataIndex: 'Agent'
                },
                {
                    hidden   : true,
                    dataIndex: 'OwnerUserId'
                },
                {   xtype   : 'actioncolumn',
                    text    : 'В архив',
                    width   : 30,
                    sortable: false,
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
                            ArchivateObjectById( rowIndex, rec.get('id'), 'ObjectsCommerceGridStore', rec.get('Street') );
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
                            RestoreObjectById( rowIndex, rec.get('id'), 'ObjectsCommerceGridStore', rec.get('Street') );
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
                            text    : 'select all',
                            hidden: true,
                            handler : function() {
                                console.log( Ext.getCmp('ObjectsCommerceGrid').getView().getNodes() );
                            }
                        },
                        {
                            text    : '',
                            iconCls : 'x-tbar-loading',
                            hidden  : false,
                            handler : function() {
                                Ext.getCmp('ObjectsCommerceGrid').getStore().load();
                            }
                        },
                        {
                            text    : 'Excel',
                            iconCls : 'ExportToExcelCls',
                            hidden  : false,
                            //inputAttrTpl: " data-qtip='Экспорт таблицы в Excel файл' ", // TODO показать подсказку! (эта не работает)
                            handler : function() {
                                if(typeof Ext.data.StoreManager.lookup('ObjectsCommerceGridStore').sorters.items[0] !== "undefined") {
                                    var SortProp = Ext.data.StoreManager.lookup('ObjectsCommerceGridStore').sorters.items[0].property;
                                } //#COLUMNSORTING
                                if(typeof Ext.data.StoreManager.lookup('ObjectsCommerceGridStore').sorters.items[0] !== "undefined") {
                                    var SortDir  = Ext.data.StoreManager.lookup('ObjectsCommerceGridStore').sorters.items[0].direction;
                                }
                                var DownloadUrl = BuildObjectExportBtnUrlString(MainAjaxDriver, CommerceObjectsGrid_Action, CommerceObjectsGrid_Active, CommerceObjectsGrid_OnlyUserId,
                                    'xls',
                                    SortProp,
                                    SortDir);
                                document.location = DownloadUrl;
                            }
                        },

                        { xtype: 'tbfill' },
                        {
                            xtype   : 'tbtext',
                            itemId  : 'ObjectsCommerceCountLabel',
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
    listeners: {
        afterrender: {
            fn: function() {
                InitUserAccessRights();
            }
        },
        dblclick : {
            fn: function() {                                    // Открытие объекта при двойном клике
                var selectedRecord  = Ext.getCmp('ObjectsCommerceGrid').getSelectionModel().getSelection()[0];
                var ObjectTabsName  = 'ObjectCommerceTabs';
                var ObjectCommerceWindow    = Ext.widget('ObjectCommerceWindow');  // создаем класс окна, а оно табы и форму
                var ObjectCommerceForm      = Ext.getCmp('ObjectCommerceForm');
                var ObjectCommerceAdditionsFormObj = Ext.getCmp('ObjectCommerceAdditionsForm');
                ObjectsGridDblclick(ObjectTabsName, ObjectCommerceWindow, ObjectCommerceForm, ObjectCommerceAdditionsFormObj, selectedRecord );
            },
            // You can also pass 'body' if you don't want click on the header or
            // docked elements
            element: 'body'
        }
    }
});
