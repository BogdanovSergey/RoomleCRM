Ext.define('crm.view.Owners.OwnersGrid', {
    extend      : 'Ext.grid.Panel',
    alias       : 'widget.OwnersGrid',
    id          : "OwnersGrid",
    flex        : 2,
    title       : 'Список собственников недвижимости',
    stripeRows  : false,
    defaults: {
        //anchor  : '100%'
    },
    initComponent: function() {
        //OpenedObjectsGrid = 'city';
        //FilterOwnerUserSelect_RealtyType = 'city';
        //GlobVars.OpenedRealtyType = 'city';
        // настраиваем кнопку-фильтр
        Ext.apply(this, {
            id      : "OwnersGrid",
            store   : 'Owners.OwnersGridStore',

            viewConfig: {
                getRowClass: function(record, index, rowParams) {
                    // делаем подсветку строки классами определенными в index.html
                    if(record.get('Color')      == 'LightRed')    { return 'ObjectsGridRowColor_LightRed'; }
                    else if(record.get('Color') == 'LightYellow') { return 'ObjectsGridRowColor_LightYellow'; }
                    else if(record.get('Color') == 'LightBrown')  { return 'ObjectsGridRowColor_LightBrown'; }
                }
            },

            tbar: [
                'Поиск по улице (введите фрагмент названия):',
                Ext.create('Ext.form.Panel', {
                        border: false,
                        defaults: {
                        },
                        bodyStyle: {
                            background: 'inherit'
                        },
                        layout: 'column',
                        vertical : false,
                        items: [
                            Ext.create('Ext.form.Panel', {
                                border: false,
                                bodyStyle: {
                                    background: 'inherit'
                                },
                                //layout: 'column',
                                //vertical : false,
                                items: [
                                    {
                                        //emptyText   : 'Агент',
                                        //fieldLabel  : ' ',
                                        //labelSeparator  : ' ',
                                        xtype       : 'textfield',
                                        id          : 'StreetSearchField',
                                        itemId      : 'StreetSearchField',
                                        //name        : 'FilterOwnerUserId',
                                        //triggerAction:  'all',
                                        //forceSelection: true,
                                        //editable    : false,
                                        allowBlank  : true,
                                        //mode        : 'remote',
                                        //displayField:'VarName',
                                        //valueField  : 'id',
                                        width       : 150
                                    }
                                ]
                            }),
                            {
                                fieldLabel  : ' ',
                                labelSeparator : ' ',
                                //fieldLabel  : 'период',
                                xtype       : 'combo',
                                id          : 'OwnersDate',
                                name        : 'OwnersDate',
                                triggerAction:  'all',
                                forceSelection: true,
                                editable    : false,
                                allowBlank  : true,
                                //queryParam  : 'OwnersDate123',
                                emptyText   : 'выберите период',
                                mode        : 'remote',
                                displayField: 'Text',
                                valueField  : 'Date',
                                //labelWidth  : 50,
                                width       : 150,
                                padding     : '0 0 0 0',
                                store: Ext.create('Ext.data.Store', {
                                        fields: [
                                            {name: 'Date'},
                                            {name: 'Text'}
                                        ],
                                        autoLoad: false,
                                        proxy: {
                                            type: 'ajax',
                                            url : 'Super.php?Action=OwnersDate',//&GetAgents=1&Active=1
                                            reader: {
                                                type: 'json'
                                            }
                                        }
                                    }
                                )
                            },

                            Ext.create('Ext.Button', {
                                text    : 'Искать',
                                handler : function() {
                                    var text        = Ext.ComponentQuery.query('#StreetSearchField')[0].getValue();
                                    var OwnersDate  = Ext.ComponentQuery.query('#OwnersDate')[0].getValue();
                                    var grid        = Ext.getCmp('OwnersGrid');
                                    var url = BuildOwnersUrlString(MainAjaxDriver, 'LoadJsonSobList', text, OwnersDate)
                                    grid.getStore().proxy.extraParams = { ChosenDate: OwnersDate };
                                    grid.getStore().proxy.url = url;
                                    grid.getStore().load();
                                    //console.log( grid.getStore().proxy );
                                }
                            }),
                            Ext.create('Ext.Button', {
                                text    : 'Сброс фильтра',
                                handler : function() {
                                    var grid = Ext.getCmp('OwnersGrid');
                                    var url  = BuildOwnersUrlString(MainAjaxDriver, 'LoadJsonSobList', '', '');// обнуляем
                                    grid.getStore().proxy.url = url;
                                    grid.getStore().load();
                                    this.up('form').getForm().reset();

                                }
                            })
                        ]
                    }
                )
            ],
            columns : [
                {
                    text        : '№',
                    dataIndex   : 'id',
                    hidden      : true
                },
                {   text     : 'Добавлено',
                    dataIndex: 'AddedDate',
                    width    : 80
                },
                {
                    text     : 'Комнат',
                    dataIndex: 'FlatType',
                    width:   40
                },
                {
                    text     : 'Метро',
                    dataIndex: 'Metro'
                },
                {
                    text     : 'Адрес',
                    dataIndex: 'Address',
                    flex     : 1
                },
                {
                    text     : 'Этажность',
                    dataIndex: 'Floors',
                    width:   50
                },
                {
                    text     : 'Площадь (о/ж/к)',
                    dataIndex: 'Square'
                },
                {
                    text     : 'Цена',
                    dataIndex: 'Price'
                },
                {
                    text     : 'Телефон',
                    dataIndex: 'Phone',
                    sortable : false
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
                                console.log( Ext.getCmp('ObjectsGrid').getView().getNodes() );
                            }
                        },
                        {
                            text    : '',
                            iconCls : 'x-tbar-loading',
                            hidden  : false,
                            handler : function() {
                                Ext.getCmp('OwnersGrid').getStore().load();
                            }
                        },
                        {
                            text    : 'Excel',
                            itemId  : 'OwnersGridExportToExcelBtn',
                            iconCls : 'ExportToExcelCls',
                            hidden  : true,
                            //inputAttrTpl: " data-qtip='Экспорт таблицы в Excel файл' ", // TODO показать подсказку! (эта не работает)
                            handler : function() {
                                //console.log( Ext.getCmp('OwnersGrid').getStore().proxy.extraParams );
                                if(typeof Ext.getCmp('OwnersGrid').getStore().proxy.url !== "undefined" ) {
                                    // стор выбран, конкретная дата-день
                                    var ChosenDate = Ext.getCmp('OwnersGrid').getStore().proxy.extraParams.ChosenDate;
                                } else {
                                    // стор не был выбран, это сегодня
                                    var ChosenDate = '';
                                }

                                //var DownloadUrl = BuildObjectExportBtnUrlString(MainAjaxDriver, CityObjectsGrid_Action, CityObjectsGrid_Active, CityObjectsGrid_OnlyUserId, 'xls');
                                var DownloadUrl = BuildSobExportUrlString(MainAjaxDriver, ChosenDate);
                                console.log( DownloadUrl );
                                document.location = DownloadUrl;
                            }
                        },

                        { xtype: 'tbfill' },
                        {
                            xtype   : 'tbtext',
                            itemId  : 'OwnersGridCountLabel',
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
        dblclick : {
            fn: function() {//todo убрать срабатывание на пустом месте таблички
                var selectedRecord  = Ext.getCmp('OwnersGrid').getSelectionModel().getSelection()[0];
                //console.log(selectedRecord.data);
//alert(selectedRecord.data.id);
                SobObjectClick(selectedRecord.data);
                /*
                var ObjectTabsName  = 'ObjectTabs';
                var ObjectWindow    = Ext.widget('ObjectWindow');  // создаем класс окна, а оно табы и форму
                var ObjectForm      = Ext.getCmp('ObjectForm');
                var ObjectAdditionsFormObj = Ext.getCmp('ObjectAdditionsForm');
                ObjectsGridDblclick(ObjectTabsName, ObjectWindow, ObjectForm, ObjectAdditionsFormObj, selectedRecord );
*/
            },
            // You can also pass 'body' if you don't want click on the header or
            // docked elements
            element: 'body'
        }
    }
});
