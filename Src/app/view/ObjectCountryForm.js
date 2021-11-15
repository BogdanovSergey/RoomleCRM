Ext.define('crm.view.ObjectCountryForm',{
    extend      : 'Ext.form.Panel',
    header      : false,
    autoScroll  : true,
    //id          : "ObjectCountryForm",
    alias       : 'widget.ObjectCountryForm',
    title       : 'Характеристики',
    url         : 'Super.php',
    bodyPadding : 10,
    defaultType : 'textfield',
    //autoRender  : true,
    initComponent: function() {
        GlobVars.RegionOrCityUpdated = 0; // адрес не редактировался #AvitoAltAddr
        Ext.apply(this, {
            id      : "ObjectCountryForm",
            items   : [
                // TODO удалить "id" внутри? itemId должно быть достаточно (file:///C:/Temp/ext-4.2.1-gpl/ext-4.2.1.883/docs/index.html#!/api/Ext.AbstractComponent-cfg-itemId)
                // заменить внешние обращения на: p1 = c.getComponent('p1');
                {
                    xtype   : 'hiddenfield',
                    name    : 'Action',
                    itemId  : 'Action',
                    value   : 'SaveCountryObjectForm'
                },
                {
                    xtype   : 'hiddenfield',
                    name    : 'EditSpecial',
                    itemId  : 'EditSpecial',
                    value   : '0' // 1 - если нужно сохранить только некоторые поля (не всю форму)
                },
                {
                    // Маркер по-умолчанию: форма открыта для объекта в москве   values: Moscow/Oblast
                    xtype   : 'hiddenfield',
                    name    : 'PositionType',
                    itemId  : 'PositionType',
                    value   : 'Moscow'
                },
                {
                    // поле по которому обновляется открытый объект
                    // при добавлении сюда добавляется новый id
                    xtype   : 'hiddenfield',
                    name    : 'LoadedObjectId',
                    itemId  : 'LoadedObjectId'
                },
                {
                    // статичное поле, используемое кнопкой "показать на карте"
                    xtype   : 'hiddenfield',
                    name    : 'Latitude',
                    itemId  : 'Latitude'
                },
                {
                    xtype      : 'fieldcontainer',
                    fieldLabel : '',
                    hideLabel  : true,
                    layout     : 'hbox',
                    defaultType: 'textfield',
                    itemId     : 'PriceAgentContainer',
                    items: [
                        {
                            xtype      : 'fieldcontainer',
                            fieldLabel : '',
                            hideLabel  : true,
                            layout     : 'hbox',
                            defaultType: 'textfield',
                            itemId     : 'PriceContainer',
                            items: [
                                {
                                    fieldLabel  : 'Цена',
                                    name        : 'Price',
                                    itemId      : 'Price',
                                    vtype       : 'DigitsVtype',
                                    allowBlank  : false,
                                    width       : 198,
                                    blankText   : 'Необходимо заполнить поле цифрами'
                                },
                                {
                                    fieldLabel  : ' ',
                                    labelSeparator : ' ',
                                    allowBlank  : false,
                                    name        : 'Currency',
                                    itemId      : 'Currency',
                                    store       : Ext.data.StoreManager.lookup('ObjectForm.CurrencyStore'),
                                    valueField  : 'id',
                                    displayField: 'Text',
                                    xtype       : 'combo',
                                    width       : 50,
                                    editable    : false,
                                    mode        : 'local',
                                    padding     :  '0 0 0 2',
                                    inputAttrTpl: " data-qclass='ToolTipPinkCls' data-qtitle='Внимание' data-qtip='Avito не поддерживает указание валюты в автоматической выгрузке! Для совместимости со всеми порталами указывайте сумму в рублях, а валюту пишите в описании' ",
                                    disabled    : false, // авито принимает только рубли
                                    listeners: {
                                        afterrender: function(combo) {
                                            Ext.ComponentQuery.query('#Currency')[0].setValue(['70']); // выбор значения по-умолчанию (70=RUB), см. storage
                                        }
                                    }
                                }
                            ]
                        },
                        {
                            xtype      : 'fieldcontainer',
                            fieldLabel : '',
                            hideLabel  : true,
                            layout     : 'hbox',
                            defaultType: 'textfield',
                            itemId     : 'AgentContainer',
                            items: [
                                {
                                    fieldLabel  : 'Агент',
                                    xtype       : 'combo',
                                    itemId      : 'OwnerUserId',
                                    name        : 'OwnerUserId',
                                    triggerAction:  'all',
                                    forceSelection: true,
                                    editable    : false,
                                    allowBlank  : false,
                                    queryParam  : 'GetAgents',
                                    mode        : 'remote',
                                    displayField:'VarName',
                                    valueField  : 'id',
                                    labelWidth  : 90,
                                    width       : 290,
                                    padding     :  '0 0 0 32',
                                    store: Ext.create('Ext.data.Store', {
                                            fields: [
                                                {name: 'id'},
                                                {name: 'VarName'}
                                            ],
                                            autoLoad: true,
                                            proxy: {
                                                type: 'ajax',
                                                url: 'Super.php?Action=GetObjectFormParams',
                                                reader: {
                                                    type: 'json'
                                                },
                                                extraParams   : {
                                                    OnlyFio  : 1,
                                                    Active   : 1,
                                                    GetAgents: 1
                                                }
                                            }
                                        }
                                    ),
                                    listeners: {
                                        select: {
                                            fn:function(combo, value) {
                                                Ext.ComponentQuery.query('#OwnerPhoneId')[0].reset();
                                            }
                                        }
                                    }
                                },
                                {
                                    hideLabel       : true,
                                    xtype           : 'combo',
                                    itemId          : 'OwnerPhoneId',
                                    name            : 'OwnerPhoneId',
                                    triggerAction   : 'all',
                                    forceSelection  : true,
                                    editable        : false,
                                    allowBlank      : false,
                                    queryParam      : 'GetObjectOwnerPhones',
                                    mode            : 'remote',
                                    displayField    : 'VarName',
                                    emptyText       : 'тел. номер',
                                    valueField      : 'id',
                                    //labelWidth: 98,
                                    width           : 120,
                                    padding         : '0 0 0 5',
                                    disabledCls     : 'DisabledCls',
                                    store           : Ext.data.StoreManager.lookup('ObjectForm.OwnerPhoneStore'),
                                    listeners: {
                                        click: {
                                            element: 'el', //bind to the underlying el property on the panel
                                            fn: function( store, operation, eOpts ) {
                                                var stor = Ext.ComponentQuery.query('#OwnerPhoneId')[0];

                                                // обновляем стор с id объекта
                                                stor.getStore().setProxy({
                                                        type        : 'ajax',
                                                        url         : GetObjectOwnerPhonesUrl,
                                                        reader      : { type: 'json' },
                                                        extraParams : {
                                                            ObjectOwnerId   : Ext.ComponentQuery.query('#OwnerUserId')[0].getValue(),
                                                            GetObjectOwnerPhones : true }
                                                    }
                                                );
                                                stor.getStore().load();
                                            }
                                        }
                                    }
                                }
                            ]
                        }
                    ]
                },
                {
                    xtype      : 'fieldcontainer',
                    fieldLabel : 'ff',
                    hideLabel : true,
                    layout     : 'hbox',
                    defaultType: 'textfield',
                    items: [
                        {
                            fieldLabel  : 'Тип цены',
                            name        : 'PriceTypeId',
                            itemId      : 'PriceTypeId',
                            store       : Ext.data.StoreManager.lookup('ObjectForm.ObjectPriceTypeStore'),
                            valueField  : 'id',
                            displayField: 'Text',
                            xtype       : 'combo',
                            width       : 250,
                            editable    : false,
                            allowBlank  : false,
                            mode        : 'local',
                            listeners: {
                                afterrender: function(combo) {
                                    Ext.ComponentQuery.query('#PriceTypeId')[0].setValue(['122']); // выбор значения по-умолчанию, см. storage
                                }
                            }
                        },
                        {
                            fieldLabel  : 'Тип сделки',
                            name        : 'DealType',
                            itemId      : 'DealType',
                            store       : Ext.data.StoreManager.lookup('ObjectForm.ObjectCountryDealTypeStore'),
                            valueField  : 'id',
                            displayField: 'Text',
                            xtype       : 'combo',
                            width       : 250,
                            labelWidth  : 90,
                            editable    : false,
                            allowBlank  : false,
                            mode        : 'local',
                            padding     :  '0 0 0 32',
                            listeners: {
                                afterrender: function(combo) {
                                    Ext.ComponentQuery.query('#DealType')[0].setValue(['128']); // выбор значения по-умолчанию (прямая продажа), см. storage
                                }
                            }
                        },
                        {
                            text  : 'Доб. корп.',
                            width       : 145,
                            padding     : '0 0 0 70',
                            xtype       : 'text'

                        },
                        {
                            xtype       : 'checkboxfield',
                            padding     : '0 0 0 5',
                            hideLabel   : true,
                            name        : 'AddCorpPhone',
                            inputValue  : '1',
                            inputAttrTpl: " data-qtip='Добавить в выгрузку корпоративный телефонный номер' ",
                            itemId      : 'AddCorpPhone'
                        }
                    ]
                },
                {
                    xtype      : 'fieldcontainer',
                    fieldLabel : 'ff',
                    hideLabel : true,
                    layout     : 'hbox',
                    defaultType: 'textfield',
                    items: [
                        {
                            fieldLabel  : 'Тип объекта',
                            width       : 250,
                            xtype       : 'combo',
                            //id          : 'ObjectType',
                            name        : 'ObjectType',
                            triggerAction:  'all',
                            forceSelection: true,
                            editable    : false,
                            allowBlank  : false,
                            queryParam  : 'GetObjectType',
                            mode        : 'remote',
                            valueField  : 'id',
                            displayField: 'Text',
                            store       : Ext.data.StoreManager.lookup('ObjectForm.ObjectCountryTypeStore'),
                            listeners: {
                                select: {
                                    fn:function(combo, value) {
                                        var SelectedId = combo.getValue();
                                        ChangeObjectFormTrigger('country', SelectedId);
                                        //console.log( combo.getValue() );
                                        /*if(SelectedId == 9 || SelectedId == 10) {
                                            // TODO это заплатка, при обновлении существующего объекта не сработает. Опять попросит стены на типе земля
                                            // это участки, здесь некоторые поля необязательны
                                            Ext.apply(Ext.ComponentQuery.query('#CountryWallsTypeId')[0], {allowBlank: true}, {});
                                            Ext.ComponentQuery.query('#CountryWallsTypeId')[0].clearInvalid();
                                        } else {
                                            // Это дома, материал стен обязателен для Авито
                                            Ext.apply(Ext.ComponentQuery.query('#CountryWallsTypeId')[0], {allowBlank: false}, {});
                                        }*/
                                    }
                                }
                            }
                        }
                    ]
                },
                /*{
                    xtype      : 'fieldcontainer',
                    fieldLabel : '_',
                    hideLabel : true,
                    layout     : 'hbox',
                    defaultType: 'textfield',
                    items: [

                    ]
                },*/
                {
                    xtype      : 'radiogroup',//fieldcontainer',
                    fieldLabel : 'Адрес',
                    layout     : 'hbox',
                    //defaultType: 'radiogroup',
                    items: [
                        {
                            boxLabel  : 'Москва',
                            name      : 'CityType',
                            inputValue: 'Moscow',
                            itemId    : 'CityType_Moscow',
                            handler: function() {
                                // открыта вкладка Москва
                                //if (Ext.getCmp('CityType_Moscow').getValue()) {
                                if( Ext.ComponentQuery.query('#CityType_Moscow')[0].getValue()) {
                                    //Ext.getCmp('OblastItems1').setVisible(false);  // скрываем поля
                                    //Ext.getCmp('OblastItems2').setVisible(false);
                                    // пропускаем эти поля при сабмите
                                    //Ext.apply(Ext.getCmp('KladrRegion'),    {value: 'Москва'}, {});
                                    //Ext.getCmp('KladrRegion').setValue('Москва');
                                    Ext.ComponentQuery.query('#KladrRegion')[0].setValue('Москва');
                                    /*Ext.apply(Ext.getCmp('KladrCity'),      {allowBlank: true}, {}); // во вкладке Москва, заполнение этих полей не нужно
                                    Ext.apply(Ext.getCmp('KladrRaion'),     {allowBlank: true}, {});
                                    Ext.apply(Ext.getCmp('KladrRegion'),    {allowBlank: true}, {});
                                    Ext.apply(Ext.getCmp('KladrPlaceType'), {allowBlank: true}, {});

                                    Ext.getCmp('PositionType').setValue('Moscow');*/ // маркер определяющий что объект в Москве
                                }
                                return;
                            }
                        }, {
                            boxLabel  : 'Область',
                            name      : 'CityType',
                            inputValue: 'Oblast',
                            checked   : true,
                            itemId    : 'CityType_Oblast',
                            padding   : '0 0 0 5',
                            handler: function() {
                                // открыта вкладка Область
                                //if (Ext.getCmp('CityType_Oblast').getValue()) {
                                if( Ext.ComponentQuery.query('#CityType_Oblast')[0].getValue()) {
                                    //Ext.getCmp('OblastItems1').setVisible(true); // открываем поля
                                    //Ext.getCmp('OblastItems2').setVisible(true);
                                    //Ext.apply(Ext.getCmp('KladrRegion'),    {value: 'Московская область'}, {});
                                    //Ext.getCmp('KladrRegion').setValue('Московская область');
                                    Ext.ComponentQuery.query('#KladrRegion')[0].setValue('Московская область');
                               /*     Ext.apply(Ext.getCmp('KladrCity'),      {allowBlank: false}, {}); // во вкладке область помечаем обязательные поля
                                    Ext.apply(Ext.getCmp('KladrRaion'),     {allowBlank: false}, {});
                                    Ext.apply(Ext.getCmp('KladrRegion'),    {allowBlank: false}, {});
                                    Ext.apply(Ext.getCmp('KladrPlaceType'), {allowBlank: false}, {});

                                    Ext.getCmp('PositionType').setValue('Oblast');   */ // маркер определяющий что объект в области
                                }
                                return;
                            }
                        },
                        {
                            xtype   : 'text',
                            width   : 270,
                            text    : ' '
                        },
                        Ext.create('Ext.Button', {
                            itemId  : 'GeoWinBtn',
                            iconCls : 'MapCls',
                            disabled: true,
                            text    : 'Посмотреть на карте',
                            padding : '0 0 0 0',
                            handler : function() {
                                // показываем окно с картой
                                // ориентируемся на значение формы Latitude
                                ShowGeoWin(Ext.ComponentQuery.query('#Latitude')[0].value, Ext.ComponentQuery.query('#LoadedObjectId')[0].value);


                            }
                        })
                    ]

                },
                {
                    xtype      : 'fieldcontainer',
                    fieldLabel      : ' ',
                    labelSeparator  : ' ',
                    layout     : 'hbox',
                    defaultType: 'textfield',
                    //id         : 'OblastItems1',
                    hidden     : false,
                    items: [
                        {
                            fieldLabel: 'Регион',
                            itemId:     'KladrRegion',
                            name:       'KladrRegion',
                            value:      'Московская область',
                            allowBlank  : false,
                            width       : 265,
                            xtype       : 'combobox',
                            anchor      : '100%',
                            hideTrigger : true,
                            displayField: 'Name',
                            valueField  : 'Name',
                            minChars    : 3,
                            queryDelay  : 250,
                            store       : Ext.data.StoreManager.lookup('ObjectForm.KladrStore'),
                            queryParam  : 'KladrRegion',
                            typeAheadDelay: 200,
                            inputAttrTpl: " data-qtip='Область, край, республика. Пример: \"<b>Тульская область</b>\", \"<b>Краснодарский край</b>\" ' ",
                            listeners: {
                                change: function(combo, records, eOpts) {
                                    GlobVars.RegionOrCityUpdated = 1; // TODO подумать? #AvitoAltAddr
                                }
                            }
                        },
                        {
                            fieldLabel  : 'Район',
                            allowBlank  : false,
                            itemId      : 'KladrRaion',
                            //id          : 'KladrRaion',
                            name        : 'KladrRaion',
                            padding     : '0 0 0 30',
                            width       : 300,
                            xtype       : 'combobox',
                            anchor      : '100%',
                            hideTrigger : true,
                            displayField: 'Name',
                            valueField  : 'Name',
                            minChars    : 3,
                            queryDelay  : 250,
                            store       : Ext.data.StoreManager.lookup('ObjectForm.KladrStore'),
                            queryParam  : 'KladrRaion',
                            typeAheadDelay: 200,
                            inputAttrTpl: " data-qtip='Пример: \"<b>Пушкинский район</b>\"' "
                        }
                    ]
                },
                {
                    xtype           : 'fieldcontainer',
                    fieldLabel      : ' ',
                    labelSeparator  : ' ',
                    layout          : 'hbox',
                    defaultType     : 'textfield',
                    hidden          : false,
                    //id              : 'OblastItems2',
                    items: [
                        {
                            allowBlank  : false,
                            fieldLabel  : 'Нас. пункт',
                            blankText   : 'Обязательный параметр',
                            inputAttrTpl: " data-qtip='Только название населенного пункта, напр.: \"<b>Химки</b>\", \"<b>Пушкино</b>\", \"<b>Иваново</b>\"' ",
                            name        : 'KladrCity',
                            itemId      : 'KladrCity',
                            xtype       : 'combobox',
                            anchor      : '100%',
                            width       : 265,
                            hideTrigger : true,
                            valueField  : 'Name',
                            displayField: 'Name',
                            minChars    : 3,
                            queryDelay  : 250,
                            store       : Ext.data.StoreManager.lookup('ObjectForm.KladrStore'),
                            queryParam  : 'KladrCity',
                            typeAheadDelay: 200,
                            value       : 'Москва',
                            listeners   : {
                                change: function(combo, records, eOpts) {
                                    GlobVars.RegionOrCityUpdated = 1; // TODO подумать? #AvitoAltAddr
                                }
                            }
                            /*tpl: Ext.create('Ext.XTemplate',
                             '<tpl for=".">',
                             '<div class="x-boundlist-item">{Name} --- {Socr}</div>',
                             '</tpl>'
                             )*/
                        },
                        {
                            itemId      : 'KladrPlaceType',
                            allowBlank  : false,
                            fieldLabel  : 'Тип нас. пункта',
                            name        : 'PlaceType',
                            xtype       : 'combobox',
                            padding     : '0 0 0 30',
                            width       : 300,
                            anchor      : '100%',
                            hideTrigger : true,
                            displayField: 'Name',
                            minChars    : 1,
                            queryDelay  : 250,
                            store       : Ext.data.StoreManager.lookup('ObjectForm.KladrStore'),
                            queryParam  : 'KladrPlaceType',
                            typeAheadDelay: 200,
                            valueField  : 'Name',
                            editable    : true,
                            inputAttrTpl: " data-qtip='Заполняйте внимательно, не перепутайте такие значения как, например: <b>Село</b> и <b>Деревня</b>.' "
                        }
                    ]
                },
                {
                    itemId      : 'AltCityFormItem',
                    xtype       : 'fieldcontainer',
                    fieldLabel  : '_',
                    hideLabel   : true,
                    layout      : 'hbox',
                    defaultType : 'textfield',
                    hidden      : true,
                    items: [
                        {
                            itemId      : 'AltCityName',
                            name        : 'AltCityName',    // #AvitoAltAddr
                            allowBlank  : true,
                            fieldLabel  : 'Ближайший  населенный пункт к вашему объекту',
                            xtype       : 'combobox',
                            padding     : '0 0 0 105',
                            labelWidth  : 395,
                            width       : 590,
                            minChars    : 1,
                            queryDelay  : 250,
                            anchor      : '100%',
                            hideTrigger : false,
                            store       : Ext.data.StoreManager.lookup('ObjectForm.AvitoAltCityStore'),
                            queryParam  : 'AltCityName',
                            typeAheadDelay: 200,
                            valueField  : 'Name',
                            displayField: 'Name',
                            editable    : false,
                            inputAttrTpl: " data-qtip='Заполняйте внимательно, не перепутайте такие значения как, например: <b>Село</b> и <b>Деревня</b>.' ",
                            listeners   : {
                                click   : {
                                    element: 'el',
                                    fn: function() {
                                        UpdateAltCityStore(); // обновить поле с новыми параметрами (если нужно) // #AvitoAltAddr
                                    }
                                }
                            }
                        }
                    ]
                },
                {
                    xtype      : 'fieldcontainer',
                    fieldLabel: ' ',
                    labelSeparator : ' ',
                    layout: 'hbox',
                    defaultType: 'textfield',
                    items: [
                        {
                            fieldLabel  : 'Шоссе',
                            name        : 'HighwayId',
                            store       : Ext.data.StoreManager.lookup('ObjectForm.ObjectCountryHighwayStore'),
                            valueField  : 'id',
                            displayField: 'Text',
                            xtype       : 'combo',
                            width       : 265,
                            allowBlank  : false,
                            editable    : false,
                            mode        : 'local'
                           // padding     :  '0 0 0 30'
                        },
                        {
                            fieldLabel  : 'км от МКАД',
                            name        : 'Distance',
                            itemId      : 'Distance',
                            vtype       : 'DigitsVtype',
                            xtype       : 'textfield',
                            allowBlank  : false,
                            width       : 160,
                            padding     :  '0 0 0 30',
                            inputAttrTpl: " data-qtip='Если объект в черте города, введите \"0\" ' "
                        }
                    ]
                },
                {
                    xtype      : 'fieldcontainer',
                    fieldLabel: ' ',
                    labelSeparator : ' ',
                    layout: 'hbox',
                    defaultType: 'textfield',
                    items: [
                        {
                            fieldLabel  : 'Улица',
                            allowBlank  : true,
                            itemId      : 'KladrStreet',
                            name        : 'Street', // TODO не то название
                            xtype       : 'combobox',
                            anchor      : '100%',
                            hideTrigger : true,
                            displayField: 'Name',
                            width       : 265,
                            minChars    : 3,
                            queryDelay  : 250,
                            store       : Ext.data.StoreManager.lookup('ObjectForm.KladrStore'),
                            queryParam  : 'KladrStreet',
                            typeAheadDelay: 200,
                            valueField  : 'Name',
                            inputAttrTpl: " data-qtip='Название улицы/переулка/площади и т.д. с указанием типа. Пример: \"<b>Школьная ул</b>\", \"<b>Красная пл</b>\". Обязательный параметр только для городов.' ",
                            listeners   : {
                                click   : {
                                    element: 'el',
                                    fn: function() {
                                        CheckAvitoCompatible();  // #AvitoAltAddr

                                    }
                                }
                            }
                        },
                        {
                            fieldLabel  : '№ дома',
                            allowBlank  : true,
                            name        : 'HouseNumber',
                            width       : 160,
                            padding     :  '0 0 0 30'
                        }
                    ]
                },
                {
                    xtype       : 'fieldcontainer',
                    fieldLabel  : 'Площадь',
                    labelSeparator  : ' ',
                    layout      : 'hbox',
                    items: [
                        {
                            fieldLabel  : 'участка в сотках',
                            name        : 'LandSquare',
                            vtype       : 'DigitsDotVtype',
                            xtype       : 'textfield',
                            allowBlank  : false,
                            width       : 145
                        },
                        {
                            fieldLabel  : 'дома в м2',
                            name        : 'SquareLiving',
                            itemId      : 'SquareLiving',
                            vtype       : 'DigitsDotVtype',
                            xtype       : 'textfield',
                            allowBlank  : false,
                            labelWidth  : 65,
                            width       : 110,
                            padding     :  '0 0 0 10',
                            inputAttrTpl: " data-qtip='Если дома на участке нет - вставьте \"0\"' "
                        },
                        {
                            fieldLabel  : 'этажей в доме',
                            name        : 'Floors',
                            itemId      : 'Floors',
                            vtype       : 'DigitsVtype',
                            xtype       : 'textfield',
                            allowBlank  : false,
                            width       : 160,
                            padding     :  '0 0 0 30'
                        }
                    ]
                },
                {
                    xtype      : 'fieldcontainer',
                    fieldLabel : 'Дополнительно',
                    layout: 'hbox',
                    items: [
                        {
                            fieldLabel: 'Назначение земли',
                            blankText:  'Обязательный параметр для Циан',
                            allowBlank  : false,
                            name        : 'LandTypeId',
                            store       : Ext.data.StoreManager.lookup('ObjectForm.ObjectCountryLandTypeStore'),
                            valueField  : 'id',
                            displayField: 'Text',
                            xtype       : 'combo',
                            labelWidth  : 130,
                            width       : 265,//265
                            editable    : false,
                            mode        : 'local'
                        },
                        {
                            fieldLabel  : 'Тип дома',
                            name        : 'CountryMaterial',
                            store       : Ext.data.StoreManager.lookup('ObjectForm.ObjectCountryMaterialStore'),
                            valueField  : 'id',
                            displayField: 'Text',
                            xtype       : 'combo',
                            width       : 300,
                            editable    : false,
                            mode        : 'local',
                            padding     :  '0 0 0 30'
                        }
                    ]
                },
                {
                    xtype      : 'fieldcontainer',
                    fieldLabel      : ' ',
                    labelSeparator  : ' ',
                    layout: 'hbox',
                    items: [

                        {
                            fieldLabel  : 'Материал стен',
                            name        : 'CountryWallsTypeId',
                            itemId      : 'CountryWallsTypeId',
                            store       : Ext.data.StoreManager.lookup('ObjectForm.CountryWallsTypeStore'),
                            allowBlank  : false,
                            valueField  : 'id',
                            displayField: 'Text',
                            xtype       : 'combo',
                            width       : 300,
                            editable    : false,
                            mode        : 'local',
                            padding     :  '0 0 0 295'
                        }
                    ]
                },
                {
                    xtype      : 'fieldcontainer',
                    fieldLabel      : ' ',
                    labelSeparator  : ' ',
                    layout: 'hbox',
                    items: [
                        {
                            fieldLabel: 'Водоснабжение',
                            name        : 'CountryWater',
                            store       : Ext.data.StoreManager.lookup('ObjectForm.ObjectCountryWaterStore'),
                            valueField  : 'id',
                            displayField: 'Text',
                            xtype       : 'combo',
                            width       : 265,
                            editable    : false,
                            mode        : 'local'
                        },
                        {
                            fieldLabel  : 'Газификация',
                            name        : 'CountryGas',
                            store       : Ext.data.StoreManager.lookup('ObjectForm.ObjectCountryGasStore'),
                            valueField  : 'id',
                            displayField: 'Text',
                            xtype       : 'combo',
                            width       : 300,
                            editable    : false,
                            mode        : 'local',
                            padding     :  '0 0 0 30'
                        }
                    ]
                },
                {
                    xtype      : 'fieldcontainer',
                    fieldLabel      : ' ',
                    labelSeparator  : ' ',
                    layout: 'hbox',
                    items: [
                        {
                            fieldLabel: 'Канализация',
                            name        : 'CountrySewer',
                            store       : Ext.data.StoreManager.lookup('ObjectForm.ObjectCountrySewerStore'),
                            valueField  : 'id',
                            displayField: 'Text',
                            xtype       : 'combo',
                            width       : 265,
                            editable    : false,
                            mode        : 'local'
                        },
                        {
                            fieldLabel  : 'Отопление',
                            name        : 'CountryHeat',
                            store       : Ext.data.StoreManager.lookup('ObjectForm.ObjectCountryHeatStore'),
                            valueField  : 'id',
                            displayField: 'Text',
                            xtype       : 'combo',
                            width       : 300,
                            editable    : false,
                            mode        : 'local',
                            padding     :  '0 0 0 30'
                        }
                    ]
                },
                {
                    xtype      : 'fieldcontainer',
                    fieldLabel      : ' ',
                    labelSeparator  : ' ',
                    layout: 'hbox',
                    items: [
                        {
                            fieldLabel  : 'Туалет',
                            name        : 'CountryToilet',
                            store       : Ext.data.StoreManager.lookup('ObjectForm.ObjectCountryToiletStore'),
                            valueField  : 'id',
                            displayField: 'Text',
                            xtype       : 'combo',
                            width       : 265,
                            editable    : false,
                            mode        : 'local'
                        },
                        {
                            fieldLabel  : 'Электроснабжение',
                            name        : 'CountryElectro',
                            store       : Ext.data.StoreManager.lookup('ObjectForm.ObjectCountryElectroStore'),
                            valueField  : 'id',
                            displayField: 'Text',
                            xtype       : 'combo',
                            labelWidth  : 130,
                            width       : 300,
                            editable    : false,
                            mode        : 'local',
                            padding     :  '0 0 0 30'
                        }
                    ]
                },
                {
                    xtype      : 'fieldcontainer',
                    fieldLabel      : ' ',
                    labelSeparator  : ' ',
                    layout: 'hbox',
                    items: [
                        {
                            fieldLabel  : 'ПМЖ',
                            name        : 'CountryPmg',
                            store       : Ext.data.StoreManager.lookup('ObjectForm.ObjectCountryPmgStore'),
                            valueField  : 'id',
                            displayField: 'Text',
                            xtype       : 'combo',
                            width       : 180,
                            editable    : false,
                            mode        : 'local'
                        },
                        {
                            fieldLabel  : 'Охрана',
                            name        : 'CountrySecure',
                            store       : Ext.data.StoreManager.lookup('ObjectForm.ObjectCountrySecureStore'),
                            valueField  : 'id',
                            displayField: 'Text',
                            xtype       : 'combo',
                            width       : 180,
                            editable    : false,
                            mode        : 'local',
                            padding     : '0 0 0 115'
                        }
                    ]
                },
                {
                    xtype           : 'fieldcontainer',
                    fieldLabel      : ' ',
                    labelSeparator  : ' ',
                    layout          : 'hbox',
                    items           : [
                        {
                            fieldLabel  : 'Гараж',
                            name        : 'CountryGarage',
                            store       : Ext.data.StoreManager.lookup('ObjectForm.ObjectCountryGarageStore'),
                            valueField  : 'id',
                            displayField: 'Text',
                            xtype       : 'combo',
                            width       : 180,
                            editable    : false,
                            mode        : 'local'
                        },
                        {
                            fieldLabel  : 'Бассейн',
                            name        : 'CountryPool',
                            store       : Ext.data.StoreManager.lookup('ObjectForm.ObjectCountryPoolStore'),
                            valueField  : 'id',
                            displayField: 'Text',
                            xtype       : 'combo',
                            width       : 180,
                            editable    : false,
                            mode        : 'local',
                            padding     : '0 0 0 115'
                        }
                    ]
                },
                {
                    xtype           : 'fieldcontainer',
                    fieldLabel      : ' ',
                    labelSeparator  : ' ',
                    layout          : 'hbox',
                    //hidden      : true,
                    items           : [
                        {
                            fieldLabel  : 'Баня',
                            name        : 'CountryBath',
                            store       : Ext.data.StoreManager.lookup('ObjectForm.ObjectCountryBathStore'),
                            valueField  : 'id',
                            displayField: 'Text',
                            xtype       : 'combo',
                            width       : 180,
                            editable    : false,
                            mode        : 'local'
                        },
                        {
                            fieldLabel  : 'Телефон',
                            name        : 'CountryPhone',
                            store       : Ext.data.StoreManager.lookup('ObjectForm.ObjectCountryPhoneStore'),
                            valueField  : 'id',
                            displayField: 'Text',
                            xtype       : 'combo',
                            width       : 180,
                            editable    : false,
                            mode        : 'local',
                            padding     : '0 0 0 115'
                        }
                    ]
                },
                {
                    xtype      : 'fieldcontainer',
                    fieldLabel : 'Клиент',
                    layout: 'hbox',
                    items: [
                        Ext.create('Ext.Button', {
                            itemId  : 'CreateObjClientBtn',
                            disabled: false,
                            height  : 22,
                            width   : 265,
                            text    : 'Создать нового клиента',
                            handler : function() {
                                var ClientWindow = Ext.widget('ClientWindow');
                                ClientWindow.setTitle(Words_CreateClientTitle);
                                ClientWindow.show();
                            }
                        }),
                        {
                            hideLabel   : true,
                            emptyText   : 'Выбрать существующего клиента',
                            xtype       : 'combo',
                            itemId      : 'OwnerClientId',
                            name        : 'OwnerClientId',
                            triggerAction : 'all',
                            forceSelection: true,
                            editable    : false,
                            allowBlank  : true, // обязательность регулируется CheckAttachClientToObjectButton()
                            queryParam  : 'GetMyClients',
                            mode        : 'remote',
                            displayField:'VarName',
                            valueField  : 'id',
                            width       : 300,
                            padding     : '0 0 0 30',
                            disabledCls : 'DisabledCls',
                            store: Ext.create('Ext.data.Store', {
                                    fields: [
                                        {name: 'id'},
                                        {name: 'VarName'}
                                    ],
                                    autoLoad: true,
                                    proxy: {
                                        type: 'ajax',
                                        url: 'Super.php?Action=GetMyClients',
                                        reader: {
                                            type: 'json'
                                        }
                                    }
                                }
                            )
                        }
                    ]
                },
                {
                    fieldLabel  : 'Описание',
                    xtype       : 'textarea',
                    name        : 'Description',
                    itemId      : 'Description',
                    hideLabel   : false,
                    anchor      : '100%',
                    overflowY   : 'auto',
                    allowBlank  : false,
                    disabledCls : 'DisabledCls',
                    //autoScroll: true,
                    maxHeight   : 100,
                    minLength   : 100,
                    minLengthText: 'Не ленитесь, составьте интересный текст! Это повысит внимание к вашему объекту! (мин. {0} символов).'
                    //height: 50,
                }
            ],
            buttons : [
                {
                    text    : 'Сохранить',
                    itemId  : 'ObjectFormSaveBtn',
                    handler : function() {
                        var SaveButtons = new Array('ObjectFormSaveBtn','ObjectFormSaveAndCloseBtn'); // Список кнопок для блокировки
                        if ( CheckObjectFormFields('country') == false ) { // если поля заполнены неправильно, выходим из сохранения
                            return;
                        }
                        TriggerSaveButtons('disable', SaveButtons );    // закрываем кнопки от двойного клика
                        CheckAvitoCompatible();                         // проверка и изменение формы для авито #AvitoAltAddr
                        Op_ExecAfterWork(                               // ждем завершения ajax запросов в предидущих ф-ях и сохраняем форму
                            function() {
                                SubmitObjectForm('ObjectCountryWindow', 'ObjectCountryTabs', 'ObjectCountryForm', 'ObjectsCountryGrid', 'ObjectCountryAdditionsForm', false);
                                TriggerSaveButtons('enable', SaveButtons );
                            }
                        );




                    }
                },
                {
                    text    : 'Сохранить и закрыть',
                    itemId  : 'ObjectFormSaveAndCloseBtn',
                    handler : function() {
                        var NeedToClose = true; // Закрытие окна
                        var SaveButtons = new Array('ObjectFormSaveBtn','ObjectFormSaveAndCloseBtn'); // Список кнопок для блокировки
                        if ( CheckObjectFormFields('country') == false ) { // если поля заполнены неправильно, выходим из сохранения
                            return;
                        }
                        TriggerSaveButtons('disable', SaveButtons );    // закрываем кнопки от двойного клика
                        CheckAvitoCompatible();                         // проверка и изменение формы для авито #AvitoAltAddr
                        Op_ExecAfterWork(                               // ждем завершения ajax запросов в предидущих ф-ях и сохраняем форму
                            function() {
                                SubmitObjectForm('ObjectCountryWindow', 'ObjectCountryTabs', 'ObjectCountryForm', 'ObjectsCountryGrid', 'ObjectCountryAdditionsForm', NeedToClose);
                                TriggerSaveButtons('enable', SaveButtons );
                            }
                        );
                    }
                },
                {
                    text    : 'Загрузка данных',
                    hidden  : true,
                    handler : function() {
                        //var b = Ext.getCmp('OwnerUserId');
                        //b.setValue(2);
                        Ext.getCmp('ObjectCountryForm').getForm().load({
                            waitMsg:'Идет Загрузка...',
                            url: 'Super.php',
                            method: 'GET',
                            params:{
                                id      : 0,
                                Action  : 'OpenObject'},
                            success: function(response, options) {
                                //ObjectForm.getForm().setValues(Ext.JSON.decode(response.data));
                                //console.log( Ext.JSON.decode(response.data));
                                //alert(response.data);
                                //alert(response.data.reminder_uid);
                                //Ext.Msg.alert(' ', 'options.result.message' + options.result.message);
                            }
                        });
                    }
                },
                {
                    text: 'Закрыть',
                    handler: function() {
                        //ObjectForm.getForm().reset();
                        Ext.getCmp('ObjectCountryWindow').close();
                    }
                }]
        } );
        this.callParent(arguments);
    }
    /*items       : [
        {
            xtype      : 'fieldcontainer',
            fieldLabel : '',
            hideLabel: true,
            layout     : 'hbox',
            defaultType: 'textfield',
            items: [
                {
                    fieldLabel: 'Цена в руб',
                    name:       'Pricefgh',
                    vtype:      'DigitsVtype',
                    allowBlank: false,
                    blankText: 'Необходимо заполнить поле цифрами'
                }]
        }]*/
});

