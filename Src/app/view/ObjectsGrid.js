Ext.define('crm.view.ObjectsGrid', { // главная таблица - список объектов
    extend      : 'Ext.grid.Panel',
    alias       : 'widget.ObjectsGrid',
    //store       : Ext.widget('GridStore').getStore(),
    id      : "ObjectsGrid",
    flex        : 1,
//    store       : 'ObjectsGridStore',//Ext.data.StoreManager.lookup('GridStore'),
    title       : 'Объекты городской недвижимости',
    stripeRows  : false,
    defaults: {
        //anchor  : '100%'
    },
    // После создания грида (см. listeners: afterrender) перепроверяем права для коррекции вида // InitUserAccessRights();
    initComponent: function() {
        OpenedObjectsGrid                = 'city';
        FilterOwnerUserSelect_RealtyType = 'city';
        GlobVars.OpenedRealtyType        = 'city';
        // настраиваем кнопку-фильтр
        Ext.apply(this, {
            id      : "ObjectsGrid",
            store   : 'ObjectsGridStore',

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
                        //console.log(Ext.getCmp('ObjectsGrid'));
                        var ObjectWindow = Ext.widget('ObjectWindow');
                        //var ObjectTabs = Ext.widget('ObjectTabs');
                        //    WhileOpeningTheObject();
                        ObjectWindow.setTitle(Words_CreateObjectTitle);
                        ObjectWindow.show();

                        //    ObjectTabs.down('#ObjectPhotosTab').setDisabled(true); // новому объекту сначала нужно сохранить характеристики, затем открыть возм загрузки фото
                        //Ext.getCmp('SquareLiving').setFieldLabel('Жилая'); // приводим в порядок форму (могли быть оставлны изменения при редактировании объектов)
                        //Ext.getCmp('RoomsSellRow').setVisible(false);
                        //Ext.apply(Ext.getCmp('RoomsSell'), {allowBlank: true}, {});

                        //Ext.data.StoreManager.lookup('ObjectForm.CurrencyStore')
                        //   Ext.ComponentQuery.query('#Currency')[0].setValue(  Ext.data.StoreManager.lookup('ObjectForm.CurrencyStore').getAt('0').get('RUB') );

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
                            listeners: {
                                specialkey: function (field, e) {
                                    if (field.getValue() != 'null') {
                                        if (e.getKey() === e.ENTER ) {
                                            var ObjectId = Ext.ComponentQuery.query('#QuickId')[0].getValue();
                                            if( ObjectId > 0) {
                                                //alert( ObjectId );

                                                Ext.Ajax.request({
                                                    type    : 'ajax',
                                                    url     : QuickObjectQueryById,
                                                    params  : {
                                                        ObjectId : ObjectId
                                                    },
                                                    success: function(response, opts) {

                                                        selectedRecord = { data : {id : ObjectId} }; // заносим в объект id (для совместимости в ф-ии ObjectsGridDblclick() )
                                                        console.log('Открываем объект №'+selectedRecord.data.id);
                                                        // ставим параметры
                                                        var Obj = Ext.decode(response.responseText);
                                                        if( Obj.RealtyType == 'city') {
                                                            var ObjectTabsName  = 'ObjectTabs';
                                                            var ObjectWindow    = Ext.widget('ObjectWindow');  // создаем класс окна, а оно табы и форму
                                                            var ObjectForm      = Ext.getCmp('ObjectForm');
                                                            var ObjectAdditionsFormObj = Ext.getCmp('ObjectAdditionsForm');

                                                        } else if( Obj.RealtyType == 'country') {
                                                            var ObjectTabsName  = 'ObjectCountryTabs';
                                                            var ObjectWindow    = Ext.widget('ObjectCountryWindow');  // создаем класс окна, а оно табы и форму
                                                            var ObjectForm      = Ext.getCmp('ObjectCountryForm');
                                                            var ObjectAdditionsFormObj = Ext.getCmp('ObjectCountryAdditionsForm');
                                                            //if(Obj.RealtyType != OpenedObjectsGrid) {
                                                                //alert(Obj.RealtyType + ' - ' + OpenedObjectsGrid);
                                                            //}

                                                        } else {
                                                            alert('Obj.RealtyType unknown');
                                                        }
                                                        //alert(Obj.RealtyType + ' - ' + OpenedObjectsGrid);

                                                        //console.log(selectedRecord);
                                                        //ChangeGridTrigger(OpenedObjectsGrid, Obj.RealtyType);
                                                        //console.log(selectedRecord);
                                                        //setTimeout(function(ObjectTabsName, ObjectWindow, ObjectForm, ObjectAdditionsFormObj, selectedRecord) {
                                                            //console.log(selectedRecord);
                                                            //ObjectsGridDblclick( ObjectTabsName, ObjectWindow, ObjectForm, ObjectAdditionsFormObj, selectedRecord );
                                                        //}, 3500);


                                                        function a(callback) {
                                                            ChangeGridTrigger(OpenedObjectsGrid, Obj.RealtyType);

                                                            if (callback) {
                                                                console.log(callback);
                                                                callback();
                                                            }
                                                        }

                                                        a(function () {
                                                                setTimeout(function() {
                                                                     selectedRecord = { data : {id : 380} };
                                                                     ObjectTabsName  = 'ObjectCountryTabs';
                                                                     ObjectWindow    = Ext.widget('ObjectCountryWindow');  // создаем класс окна, а оно табы и форму
                                                                     ObjectForm      = Ext.getCmp('ObjectCountryForm');
                                                                     ObjectAdditionsFormObj = Ext.getCmp('ObjectCountryAdditionsForm');
                                                                    ObjectsGridDblclick( ObjectTabsName, ObjectWindow, ObjectForm, ObjectAdditionsFormObj, selectedRecord );
                                                                    }, 3500);
                                                            //ObjectsGridDblclick( ObjectTabsName, ObjectWindow, ObjectForm, ObjectAdditionsFormObj, selectedRecord );
                                                        });





                                                        // переключиться на соответствующую вкладку и открыть объект
                                                        //ChangeGridTrigger(OpenedObjectsGrid, Obj.RealtyType);

                                                        /*Ext.Msg.show({
                                                            title:'Save Changes?',
                                                            msg: 'You are closing a tab that has unsaved changes. Would you like to save your changes?',
                                                            buttons: Ext.Msg.YESNOCANCEL,
                                                            icon: Ext.Msg.QUESTION
                                                        },function(){alert(1);});



                                                        Ext.Msg.prompt('Name', 'Please enter your name:', function(btn, text){
                                                            if (btn == 'ok'){

                                        if( ChangeGridTrigger(OpenedObjectsGrid, Obj.RealtyType) ) {
                                            ObjectsGridDblclick(ObjectTabsName, ObjectWindow, ObjectForm, ObjectAdditionsFormObj, selectedRecord );
                                        }

                                                            }
                                                        });
                                                        */
                                                        //if( ChangeGridTrigger(OpenedObjectsGrid, Obj.RealtyType) ) {
                                                            //ObjectsGridDblclick(ObjectTabsName, ObjectWindow, ObjectForm, ObjectAdditionsFormObj, selectedRecord );
                                                        //}


                                                        /*ChangeGridTriggerAsync( function () {
                                                            ChangeGridTrigger(OpenedObjectsGrid, Obj.RealtyType)
                                                        });
                                                        asyncDbAccess(query, function (dbData) {
                                                            processFsAndDb(fileData, dbData);
                                                        });*/

                                                        /////////////////////////////
                                                        /*ChangeGridTrigger( OpenedObjectsGrid, Obj.RealtyType, function () {
                                                            console.log('neXt');
                                                            ObjectsGridDblclick(ObjectTabsName, ObjectWindow, ObjectForm, ObjectAdditionsFormObj, selectedRecord );
                                                        });*/
                                                        /*var ggg = function() {alert(11111111);
                                                            ChangeGridTrigger(OpenedObjectsGrid, Obj.RealtyType, function() {alert(22222);
                                                                ObjectsGridDblclick(ObjectTabsName, ObjectWindow, ObjectForm, ObjectAdditionsFormObj, selectedRecord );
                                                            });
                                                        }
                                                        ggg();*/

                                                    },
                                                    failure: function(response, opts) {
                                                        alert(123);
                                                    }
                                                });


                                            }

                                        }

                                    }
                                }
                            }
                        }
                    ]
                },
                '-',
                {
                    xtype     : 'checkboxfield',
                    boxLabel  : 'Рекламные функции',
                    name      : 'AdsFuncsCityGridChkbx',
                    itemId    : 'AdsFuncsCityGridChkbx',
                    checked   : true,
                    handler: function() {
                        // открываем / закрываем
                        var val = Ext.ComponentQuery.query('#AdsFuncsCityGridChkbx')[0].getValue();
                        if(val == true) {
                            AdColumnsTrigger('setVisible');
                        } else {
                            AdColumnsTrigger('setInvisible');
                        }
                    }
                },
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
                                        var ObjGrid = Ext.getCmp('ObjectsGrid');
                                        CityObjectsGrid_Active = 1;
                                        ObjGrid.getStore().proxy.url = BuildGridUrlString(MainAjaxDriver, CityObjectsGrid_Action, CityObjectsGrid_Active, CityObjectsGrid_OnlyUserId);
                                        //ObjGrid.store.setProxy( ActiveCityObjectsGridProxy );   // меняем ссылку для забора только АКТИВНЫХ объектов
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
                                        var ObjGrid = Ext.getCmp('ObjectsGrid');
                                        CityObjectsGrid_Active = 0; // глоб параметр для грида
                                        ObjGrid.getStore().proxy.url = BuildGridUrlString(MainAjaxDriver, CityObjectsGrid_Action, CityObjectsGrid_Active, CityObjectsGrid_OnlyUserId);

                                        //ObjGrid.getStore().setProxy( ArchivedCityObjectsGridProxy ); // меняем ссылку для забора только АРХИВНЫХ объектов
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
                                        CityObjectsGrid_OnlyUserId = SelectedId; // обновляем глоб переменные для построения url
                                        // обновляем url списка фоток
                                        var grid = Ext.getCmp('ObjectsGrid');
                                        grid.getStore().proxy.url = BuildGridUrlString(MainAjaxDriver, CityObjectsGrid_Action, CityObjectsGrid_Active, CityObjectsGrid_OnlyUserId);
                                        grid.getStore().load();
                                    }
                                }
                            }
                        },
                        Ext.create('Ext.Button', {
                            //iconCls : 'ClrFilterCls',
                            text    : 'Сброс фильтра',
                            handler : function() {
                                CityObjectsGrid_OnlyUserId = '';
                                var grid = Ext.getCmp('ObjectsGrid');

                                // сброс менюшки-фильтра
                                Ext.getCmp('FilterOwnerUserId').reset();

                                if(CityObjectsGrid_Active == 1) {
                                    // грид обновим вручную (т.к. уже нажата радио кнопка)
                                    var url  = BuildGridUrlString(MainAjaxDriver, CityObjectsGrid_Action, CityObjectsGrid_Active, CityObjectsGrid_OnlyUserId);
                                    grid.getStore().proxy.url = url;
                                    grid.getStore().load();
                                } else {
                                    // грид обновится сам, по собитию изменения кнопки
                                    this.up('form').getForm().reset();
                                }
                                // сбрасываем
                                //alert(s);
                                //grid.getStore().proxy.url = MainAjaxDriver + '?' + ActiveCityObjectsGridProxyParams;
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
                /*{   text        : ' ',
                    width       : 20,
                    dataIndex   : 'checkbox',
                    xtype       : 'checkcolumn',
                    editor: {
                        xtype   : 'checkbox',
                        cls     : 'x-grid-checkheader-editor'
                    },

                },*/
                /*renderer: function(value) {
                 if(value > 0) {
                 //<img src="icons/bullet_picture.png" width="10">
                 return Ext.String.format('<input type="checkbox" class="x-grid-cell-checkcolumn">', value, value);
                 }
                 }
                 listeners: {
                 checkchange: function (column, recordIndex, checked) {
                 var rec = Ext.getCmp('ObjectsGrid').getStore().getAt(recordIndex);
                 console.log( column );
                 console.log( rec );
                 // UpdateAdTarifByObjectId(rec.get('id'), 'winner', checked);
                 }
                 }*/
                /*renderer: function(value) {
                 if(value > 0) {
                 //        return Ext.String.format('{1}', value, value);
                 }
                 }*/
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
                    text     : 'Объект',
                    dataIndex: 'ObjectTypeName',
                    width:   60
                },
                {
                    text     : 'Тип',
                    dataIndex: 'ObjectAgeType',
                    width:   40
                },
                {
                    text     : 'Комнат',
                    dataIndex: 'RoomsCount',
                    width:   40
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
                    text     : 'Этажность',
                    dataIndex: 'Floors',
                    width:   50
                },
                {
                    text     : 'Площадь (о/ж/к)',
                    dataIndex: 'Squares'
                },
                {
                    text     : 'Цена',
                    dataIndex: 'Price'
                },
                {   text     : 'Сайт агентства',  width   : 20,
                    dataIndex: 'TrfAnSiteFree',   xtype   : 'checkcolumn',
                    itemId   : 'TrfAnSiteFree',
                    disabled : false,
                    hidden   : true,
                    menuDisabled: true,
                    editor: {
                        xtype   : 'checkbox',
                        cls     : 'x-grid-checkheader-editor'
                    },
                    listeners: {
                        checkchange: function (column, recordIndex, checked) {//Ext.ComponentQuery.query('#TrfAnSiteFree')[0].setValue(false);
                            console.log('checkchange event');
                            TrfCheckchangeEvent('ObjectsGrid','TrfAnSiteFree', recordIndex, checked);
                        }
                    },
                    renderer: function(value, metaData, record, rowIndex, colIndex, store, view) {
                        return TrfRenderer('ObjectsGrid', value, rowIndex);
                    }
                },
                {   text     : 'Winner',        width   : 20,
                    dataIndex: 'TrfWinner',  xtype   : 'checkcolumn',
                    itemId   : 'TrfWinner',
                    disabled : false,
                    hidden   : true,
                    menuDisabled: true,
                    editor: {
                        xtype   : 'checkbox',
                        cls     : 'x-grid-checkheader-editor' },
                    listeners: {
                        checkchange: function (column, recordIndex, checked) {
                            console.log('checkchange event');
                            TrfCheckchangeEvent('ObjectsGrid','TrfWinner', recordIndex, checked);
                        }
                    },
                    renderer: function(value, metaData, record, rowIndex, colIndex, store, view) {
                        return TrfRenderer('ObjectsGrid', value, rowIndex);
                    }
                },
                {   text     : 'Циан',          width   : 20,
                    dataIndex: 'TrfCian',    xtype   : 'checkcolumn',
                    itemId   : 'TrfCian',
                    disabled : false,
                    hidden   : true,
                    menuDisabled: true,
                    editor: {
                        xtype   : 'checkbox',
                        cls     : 'x-grid-checkheader-editor' },
                    listeners: {
                        checkchange: function (column, recordIndex, checked) {
                            console.log('checkchange event');
                            TrfCheckchangeEvent('ObjectsGrid','TrfCian', recordIndex, checked);
                        }
                    },
                    renderer: function(value, metaData, record, rowIndex, colIndex, store, view) {
                        return TrfRenderer('ObjectsGrid', value, rowIndex);
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
                            // TODO при нажатии на эту галку, должен отжиматься AdPortCian
                            console.log('checkchange event');
                            TrfCheckchangeEvent('ObjectsGrid','TrfCianPremium', recordIndex, checked);

                            //Ext.Msg.alert(  'Установлен ЦИАН-премиум статус для объекта №' + rec.get('id'),
                            //                'Вы установили ЦИАН-премиум статус для объекта:<br><br><b>' + rec.get('City') + ' ' + rec.get('Street') +'</b> - ' + rec.get('Agent') + '<br><br><img src="icons/money.png"> За данную операцию взимается дополнительная плата. <br>Проконтроллируйте выход объявления на портале.');
                        }
                    },
                    renderer: function(value, metaData, record, rowIndex, colIndex, store, view) {
                        return TrfRenderer('ObjectsGrid', value, rowIndex);
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
                            TrfCheckchangeEvent('ObjectsGrid','TrfAvito', recordIndex, checked);
                        }

                    },
                    renderer: function(value, metaData, record, rowIndex, colIndex, store, view) {
                        return TrfRenderer('ObjectsGrid', value, rowIndex);
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
                            TrfCheckchangeEvent('ObjectsGrid','TrfNavigatorFree', recordIndex, checked);
                        }
                    },
                    renderer: function(value, metaData, record, rowIndex, colIndex, store, view) {
                        return TrfRenderer('ObjectsGrid', value, rowIndex);
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
                            TrfCheckchangeEvent('ObjectsGrid','TrfRbcFree', recordIndex, checked);
                        }
                    },
                    renderer: function(value, metaData, record, rowIndex, colIndex, store, view) {
                        return TrfRenderer('ObjectsGrid', value, rowIndex);
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
                            TrfCheckchangeEvent('ObjectsGrid','TrfAfy', recordIndex, checked);
                        }
                    },
                    renderer: function(value, metaData, record, rowIndex, colIndex, store, view) {
                        return TrfRenderer('ObjectsGrid', value, rowIndex);
                    }
                    /*handler: function(grid, rowIndex, colIndex) {
                        alert(rowIndex);
                    }*/
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
                            TrfCheckchangeEvent('ObjectsGrid','TrfYandex', recordIndex, checked);
                        }
                    },
                    renderer: function(value, metaData, record, rowIndex, colIndex, store, view) {
                        return TrfRenderer('ObjectsGrid', value, rowIndex);
                    }
                    /*handler: function(grid, rowIndex, colIndex) {
                     alert(rowIndex);
                     }*/
                },
                {
                    text     : 'Затраты на рекламу',
                    dataIndex: 'AdCosts',
                    itemId   : 'AdCosts',
                    menuDisabled: true,
                    hidden   : false,
                    width    : 40
                },
                {
                    text     : 'Агент',
                    dataIndex: 'Agent'
                },
                {
                    hidden   : true,
                    dataIndex: 'OwnerUserId'
                },
                {   xtype       : 'actioncolumn',
                    text        : 'В архив',
                    width       : 30,
                    dataIndex   : 'DeleteColumn',
                    menuDisabled: true,
                    items       : [{
                        tooltip : 'В архив',
                        //icon:'icons/cross.png',
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
                            ArchivateObjectById( rowIndex, rec.get('id'), 'ObjectsGridStore', rec.get('Street') );
                        }
                    }]
                },
                {   xtype       : 'actioncolumn',
                    width       : 30,
                    text        : 'Восстановить',
                    dataIndex   : 'RestoreColumn',
                    menuDisabled: true,
                    hidden      : true,
                    items       : [{
                        //icon:'icons/arrow_undo.png',
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
                            RestoreObjectById( rowIndex, rec.get('id'), 'ObjectsGridStore', rec.get('Street') );
                        }
                    }]
                }
            ],
            dockedItems : [
                /*{// дефолтная панель пагинации
                    xtype       : 'pagingtoolbar',
                    store       : 'ObjectsGridStore', //Ext.data.StoreManager.lookup('GridStore'),   // same store GridPanel is using
                    dock        : 'bottom',
                    displayInfo : true,
                    displayMsg  : 'Всего объектов: {2}',
                    emptyMsg    : 'Не найдено ни одного объекта'
                    / *prependButtons: true,
                     items:[{
                     xtype:'button',
                     text:'nextpage'
                     }* /
                },*/
                {
                    xtype       : 'toolbar',
                    dock        : 'bottom',
                    items: [
                        {
                            text    : 'select all',
                            hidden: true,
                            handler : function() {
                                console.log( Ext.getCmp('ObjectsGrid').getView().getNodes() );

                                /*var tc = Ext.data.StoreManager.lookup('ObjectsGridStore').getTotalCount();
                                for (var c = 0; c < tc; c++) {
                                    var rec = Ext.getCmp('ObjectsGrid').getStore().getAt(c);
                                    if(typeof rec !== "undefined") {
                                        console.log( rec );
                                        console.log( rec.get('id')  );
                                        console.log( rec.down('checkbox')  );
                                        console.log( Ext.getCmp('ObjectsGrid').getStore().getAt(c).get('checkbox').setValue(true)  );
                                        //a s; df
                                       // rec.get('checkbox').setValue(true);
                                    }
                                }*/
                            //    var rec = Ext.getCmp('ObjectsGrid').getStore().getAt(recordIndex);


                                //alert(  Ext.data.StoreManager.lookup('ObjectsGridStore').data.store.totalCount );
                                //var rec = Ext.getCmp('ObjectsGrid').getStore().getAt(recordIndex);
                                //UpdateAdTarifByObjectId(rec.get('id'), 'avito', checked);  }

                             //   var a = Ext.getStore("ObjectsGridStore");
                                //Ext.data.StoreManager.lookup('ObjectsGridStore').each(function(rec){
                          //      a.each(function(rec){
                                //Ext.getCmp('ObjectsGrid').getStore().each(function(rec){
                                //console.log( Ext.getCmp('ObjectsGrid').store );
                                //console.log( Ext.data.StoreManager.lookup('ObjectsGridStore') );//.each(function(rec){
                                    //html = Ext.get("objectvals").dom.innerHTML;
                                 //   console.log( rec.data.id );
                           //     });

                            }
                        },
                        {
                            text    : '',
                            iconCls : 'x-tbar-loading',
                            hidden  : false,
                            handler : function() {
                                Ext.getCmp('ObjectsGrid').getStore().load();
                            }
                        },
                        {
                            text    : 'Excel',
                            iconCls : 'ExportToExcelCls',
                            hidden  : false,
                            //inputAttrTpl: " data-qtip='Экспорт таблицы в Excel файл' ", // TODO показать подсказку! (эта не работает)
                            handler : function() {                                          // TODO сделать сортировку по неск столбцам

                                if(typeof Ext.data.StoreManager.lookup('ObjectsGridStore').sorters.items[0] !== "undefined") {
                                    var SortProp = Ext.data.StoreManager.lookup('ObjectsGridStore').sorters.items[0].property;
                                }
                                if(typeof Ext.data.StoreManager.lookup('ObjectsGridStore').sorters.items[0] !== "undefined") {
                                    var SortDir  = Ext.data.StoreManager.lookup('ObjectsGridStore').sorters.items[0].direction;
                                }
                                var DownloadUrl = BuildObjectExportBtnUrlString(MainAjaxDriver, CityObjectsGrid_Action, CityObjectsGrid_Active, CityObjectsGrid_OnlyUserId,
                                                        'xls',
                                                        SortProp,
                                                        SortDir);
                                document.location = DownloadUrl;
                            }
                        },

                        { xtype: 'tbfill' },
                        {
                            xtype   : 'tbtext',
                            itemId  : 'ObjectsVtorCountLabel',
                            text    : 'Объекты не загружены', // сменится на "Всего объектов: ххх" через контроллер
                            style   : {
                                //textalign : 'right'
                            }
                        }

                    ]
                }
            ]
            //,
            //listeners : {
                /*
                viewready: function( grid ) {

                    var map = new Ext.KeyMap(grid.getEl(),
                        [{
                            key: "c",
                            ctrl:true,
                            fn: function(keyCode, e) {
                                alert('KeyMap');
                                console.log(keyCode);
                                console.log(e);
                                //var recs = grid.getSelectionModel().getSelection();
                            }
                        }
                        ]);
                },*/
            //}
            /*onCheckcolumnBeforeCheckChange: function(checkcolumn, rowIndex, checked, eOpts) {
                //var row = this.getView().getRow(rowIndex),
                //    record = this.getView().getRecord(row);
                //console.log( this.getView().getRow(rowIndex) );
                alert(321);
                //return (record.get('oldUser') != 'N');
            }*/
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
                var selectedRecord  = Ext.getCmp('ObjectsGrid').getSelectionModel().getSelection()[0];
                var ObjectTabsName  = 'ObjectTabs';
                var ObjectWindow    = Ext.widget('ObjectWindow');  // создаем класс окна, а оно табы и форму
                var ObjectForm      = Ext.getCmp('ObjectForm');
                var ObjectAdditionsFormObj = Ext.getCmp('ObjectAdditionsForm');
                ObjectsGridDblclick(ObjectTabsName, ObjectWindow, ObjectForm, ObjectAdditionsFormObj, selectedRecord );

            },
            // You can also pass 'body' if you don't want click on the header or
            // docked elements
            element: 'body'
        }
    }
    /*stateId     : 'CityObjectsGridState',
    stateful: true, // state should be preserved
    *///stateEvents: ['columnresize', 'columnmove', 'show', 'hide' /*, add more events here as suits you*/] //  which event should cause store state



    /*, renderer: function(value) {
        value = '<span style="color:#00ff00;">' + value + '</span>';
        return value;
    }*/

});
