Ext.define('crm.view.Settings.AdPricesForm', {
    extend      : 'Ext.form.Panel',
    header      : false,
    autoScroll  : true,
    alias       : 'widget.AdPricesForm',
    title       : '',
    url         : 'Super.php',
    bodyPadding : 10,
    //id          : "ObjectForm",
    defaultType : 'textfield',
    initComponent: function() {
        //GlobVars.RegionOrCityUpdated = 0; // маркер: адрес не редактировался #AvitoAltAddr
        Ext.apply(this, {
            id          : "AdPricesForm",
            items   : [
                {
                    xtype: 'hiddenfield',
                    name: 'Action',
                    value: 'SaveAdPricesForm'
                },

                {
                    xtype   : 'text',
                    width   : 600,
                    height  : 30,
                    text    : 'Включите нужные порталы и укажите цену за выгрузку одного объявления в сутки:'
                },
                {
                    xtype           : 'fieldcontainer',
                    fieldLabel      : ' ',
                    labelSeparator  : ' ',
                    hideLabel       : true,
                    layout          : 'hbox',
                    padding         : '0 0 0 0',
                    width           : 750,
                    items : [
                        Ext.create('Ext.Img', {
                            src         : 'images/w.jpg',
                            height : 30
                        }),
                        {
                            xtype       : 'text',
                            width       : 100,
                            text        : 'Сайт АН:',
                            padding     : '0 0 0 20',
                        },
                        {
                            fieldLabel  : 'вкл',
                            xtype       : 'checkboxfield',
                            padding     : '0 0 0 25',
                            labelWidth  : 40,
                            name        : 'TrfAnSiteFreeActive',
                            itemId      : 'TrfAnSiteFreeActive',
                            inputValue  : '1'
                        },
                        {
                            fieldLabel  : 'цена',
                            //allowBlank  : false,
                            size        : 5,
                            name        : 'TrfAnSiteFree',
                            itemId      : 'TrfAnSiteFree',
                            vtype       : 'DigitsDotVtype',
                            labelWidth  : 40,
                            width       : 100,
                            xtype       : 'textfield',
                            padding     : '0 0 0 10',
                        },
                        {
                            fieldLabel  : 'xml',
                            emptyText   : 'http://',
                            name        : 'TrfAnSiteFreeXmlLinks',
                            itemId      : 'TrfAnSiteFreeXmlLinks',
                            padding     : '0 0 0 10',
                            readOnly    : true,
                            xtype       : 'textfield',
                            labelWidth  : 40,
                            width       : 400
                        }
                    ]
                },
                {
                    xtype           : 'fieldcontainer',
                    fieldLabel      : ' ',
                    labelSeparator  : ' ',
                    hideLabel       : true,
                    layout          : 'hbox',
                    padding         : '0 0 0 0',
                    width           : 750,
                    items : [
                        Ext.create('Ext.Img', {
                            src         : 'images/sites/winner.gif',
                            height : 30
                        }),
                        {
                            xtype   : 'text',
                            width   : 100,
                            text    : 'Winner:',
                            padding : '0 0 0 20'
                        },
                        {
                            fieldLabel  : 'вкл',
                            xtype       : 'checkboxfield',
                            padding     : '0 0 0 25',
                            labelWidth  : 40,
                            name        : 'TrfWinnerActive',
                            itemId      : 'TrfWinnerActive',
                            inputValue  : '1'
                        },
                        {
                            fieldLabel  : 'цена',
                            //allowBlank  : false,
                            size        : 5,
                            name        : 'TrfWinner',
                            itemId      : 'TrfWinner',
                            vtype       : 'DigitsDotVtype',
                            labelWidth  : 40,
                            width       : 100,
                            xtype       : 'textfield',
                            padding     : '0 0 0 10',
                        },
                        {
                            fieldLabel  : 'xml',
                            emptyText   : 'http://',
                            name        : 'TrfWinnerXmlLinks',
                            itemId      : 'TrfWinnerXmlLinks',
                            padding     : '0 0 0 10',
                            readOnly    : true,
                            xtype       : 'textfield',
                            labelWidth  : 40,
                            width       : 400
                        }
                    ]
                },
                {
                    xtype           : 'fieldcontainer',
                    fieldLabel      : ' ',
                    labelSeparator  : ' ',
                    hideLabel       : true,
                    layout          : 'hbox',
                    padding         : '0 0 0 0',
                    width           : 750,
                    items : [
                        Ext.create('Ext.Img', {
                            src         : 'images/sites/cian.gif',
                            height : 30
                        }),
                        {
                            xtype   : 'text',
                            width   : 100,
                            text    : 'Cian:',
                            padding : '0 0 0 20',
                        },
                        {
                            fieldLabel  : 'вкл',
                            xtype       : 'checkboxfield',
                            padding     : '0 0 0 25',
                            labelWidth  : 40,
                            name        : 'TrfCianActive',
                            itemId      : 'TrfCianActive',
                            inputValue  : '1'
                        },
                        {
                            fieldLabel  : 'цена',
                            //allowBlank  : false,
                            size        : 5,
                            name        : 'TrfCian',
                            itemId      : 'TrfCian',
                            vtype       : 'DigitsDotVtype',
                            labelWidth  : 40,
                            width       : 100,
                            xtype       : 'textfield',
                            padding     : '0 0 0 10',
                        },
                        {
                            fieldLabel  : 'xml',
                            emptyText   : 'http://',
                            name        : 'TrfCianXmlLinks',
                            itemId      : 'TrfCianXmlLinks',
                            padding     : '0 0 0 10',
                            readOnly    : true,
                            xtype       : 'textfield',
                            labelWidth  : 40,
                            width       : 400
                        }
                    ]
                },
                {
                    xtype           : 'fieldcontainer',
                    fieldLabel      : ' ',
                    labelSeparator  : ' ',
                    hideLabel       : true,
                    layout          : 'hbox',
                    padding         : '0 0 0 0',
                    width           : 750,
                    items : [
                        Ext.create('Ext.Img', {
                            src         : 'images/sites/cian.gif',
                            height : 30
                        }),
                        {
                            xtype   : 'text',
                            width   : 100,
                            text    : 'Cian premium:',
                            padding : '0 0 0 20',
                        },
                        {
                            fieldLabel  : 'вкл',
                            xtype       : 'checkboxfield',
                            padding     : '0 0 0 25',
                            labelWidth  : 40,
                            name        : 'TrfCianPremiumActive',
                            itemId      : 'TrfCianPremiumActive',
                            inputValue  : '1'
                        },
                        {
                            fieldLabel  : 'цена',
                            //allowBlank  : false,
                            size        : 5,
                            name        : 'TrfCianPremium',
                            itemId      : 'TrfCianPremium',
                            vtype       : 'DigitsDotVtype',
                            labelWidth  : 40,
                            width       : 100,
                            xtype       : 'textfield',
                            padding     : '0 0 0 10',
                        },
                        {
                            fieldLabel  : 'xml',
                            emptyText   : 'http://',
                            name        : 'TrfCianPremiumXmlLinks',
                            itemId      : 'TrfCianPremiumXmlLinks',
                            padding     : '0 0 0 10',
                            readOnly    : true,
                            xtype       : 'textfield',
                            labelWidth  : 40,
                            width       : 400
                        }
                    ]
                },
                {
                    xtype           : 'fieldcontainer',
                    fieldLabel      : ' ',
                    labelSeparator  : ' ',
                    hideLabel       : true,
                    layout          : 'hbox',
                    padding         : '0 0 0 0',
                    width           : 750,
                    items : [
                        Ext.create('Ext.Img', {
                            src         : 'images/sites/avito.gif',
                            height : 30
                        }),
                        {
                            xtype       : 'text',
                            width       : 100,
                            text        : 'Avito:',
                            padding     : '0 0 0 20',
                        },
                        {
                            fieldLabel  : 'вкл',
                            xtype       : 'checkboxfield',
                            padding     : '0 0 0 25',
                            labelWidth  : 40,
                            name        : 'TrfAvitoActive',
                            itemId      : 'TrfAvitoActive',
                            inputValue  : '1'
                        },
                        {
                            fieldLabel  : 'цена',
                            //allowBlank  : false,
                            size        : 5,
                            name        : 'TrfAvito',
                            itemId      : 'TrfAvito',
                            vtype       : 'DigitsDotVtype',
                            labelWidth  : 40,
                            width       : 100,
                            xtype       : 'textfield',
                            padding     : '0 0 0 10',
                        },
                        {
                            fieldLabel  : 'xml',
                            emptyText   : 'http://',
                            name        : 'TrfAvitoXmlLinks',
                            itemId      : 'TrfAvitoXmlLinks',
                            padding     : '0 0 0 10',
                            readOnly    : true,
                            xtype       : 'textfield',
                            labelWidth  : 40,
                            width       : 400
                        }
                    ]
                },
                {
                    xtype           : 'fieldcontainer',
                    fieldLabel      : ' ',
                    labelSeparator  : ' ',
                    hideLabel       : true,
                    layout          : 'hbox',
                    padding         : '0 0 0 0',
                    width           : 750,
                    items : [
                        Ext.create('Ext.Img', {
                            src         : 'images/sites/afy.gif',
                            height : 30
                        }),
                        {
                            xtype       : 'text',
                            width       : 100,
                            text        : 'Afy:',
                            padding     : '0 0 0 20',
                        },
                        {
                            fieldLabel  : 'вкл',
                            xtype       : 'checkboxfield',
                            padding     : '0 0 0 25',
                            labelWidth  : 40,
                            name        : 'TrfAfyActive',
                            itemId      : 'TrfAfyActive',
                            inputValue  : '1'
                        },
                        {
                            fieldLabel  : 'цена',
                            //allowBlank  : false,
                            size        : 5,
                            name        : 'TrfAfy',
                            itemId      : 'TrfAfy',
                            vtype       : 'DigitsDotVtype',
                            labelWidth  : 40,
                            width       : 100,
                            xtype       : 'textfield',
                            padding     : '0 0 0 10',
                        },
                        {
                            fieldLabel  : 'xml',
                            emptyText   : 'http://',
                            name        : 'TrfAfyXmlLinks',
                            itemId      : 'TrfAfyXmlLinks',
                            padding     : '0 0 0 10',
                            readOnly    : true,
                            xtype       : 'textfield',
                            labelWidth  : 40,
                            width       : 400
                        }
                    ]
                },
                {
                    xtype           : 'fieldcontainer',
                    fieldLabel      : ' ',
                    labelSeparator  : ' ',
                    hideLabel       : true,
                    layout          : 'hbox',
                    padding         : '0 0 0 0',
                    width           : 750,
                    items : [
                        Ext.create('Ext.Img', {
                            src         : 'images/sites/navigator.gif',
                            height : 30
                        }),
                        {
                            xtype       : 'text',
                            width       : 100,
                            text        : 'Навигатор:',
                            padding     : '0 0 0 20',
                        },
                        {
                            fieldLabel  : 'вкл',
                            xtype       : 'checkboxfield',
                            padding     : '0 0 0 25',
                            labelWidth  : 40,
                            name        : 'TrfNavigatorFreeActive',
                            itemId      : 'TrfNavigatorFreeActive',
                            inputValue  : '1'
                        },
                        {
                            fieldLabel  : 'цена',
                            //allowBlank  : false,
                            size        : 5,
                            name        : 'TrfNavigatorFree',
                            itemId      : 'TrfNavigatorFree',
                            vtype       : 'DigitsDotVtype',
                            labelWidth  : 40,
                            width       : 100,
                            xtype       : 'textfield',
                            padding     : '0 0 0 10',
                        },
                        {
                            fieldLabel  : 'xml',
                            emptyText   : 'http://',
                            name        : 'TrfNavigatorFreeXmlLinks',
                            itemId      : 'TrfNavigatorFreeXmlLinks',
                            padding     : '0 0 0 10',
                            readOnly    : true,
                            xtype       : 'textfield',
                            labelWidth  : 40,
                            width       : 400
                        }
                    ]
                },
                {
                    xtype           : 'fieldcontainer',
                    fieldLabel      : ' ',
                    labelSeparator  : ' ',
                    hideLabel       : true,
                    layout          : 'hbox',
                    padding         : '0 0 0 0',
                    width           : 750,
                    items : [
                        Ext.create('Ext.Img', {
                            src         : 'images/sites/rbc.gif',
                            width : 40
                        }),
                        {
                            xtype       : 'text',
                            width       : 100,
                            text        : 'РБК:',
                            padding     : '0 0 0 10',
                        },
                        {
                            fieldLabel  : 'вкл',
                            xtype       : 'checkboxfield',
                            padding     : '0 0 0 20',
                            labelWidth  : 40,
                            name        : 'TrfRbcFreeActive',
                            itemId      : 'TrfRbcFreeActive',
                            inputValue  : '1'
                        },
                        {
                            fieldLabel  : 'цена',
                            //allowBlank  : false,
                            size        : 5,
                            name        : 'TrfRbcFree',
                            itemId      : 'TrfRbcFree',
                            vtype       : 'DigitsDotVtype',
                            labelWidth  : 40,
                            width       : 100,
                            xtype       : 'textfield',
                            padding     : '0 0 0 10',
                        },
                        {
                            fieldLabel  : 'xml',
                            emptyText   : 'http://',
                            name        : 'TrfRbcFreeXmlLinks',
                            itemId      : 'TrfRbcFreeXmlLinks',
                            padding     : '0 0 0 10',
                            readOnly    : true,
                            xtype       : 'textfield',
                            labelWidth  : 40,
                            width       : 400
                        }
                    ]
                },
                {
                    xtype           : 'fieldcontainer',
                    fieldLabel      : ' ',
                    labelSeparator  : ' ',
                    hideLabel       : true,
                    layout          : 'hbox',
                    padding         : '0 0 0 0',
                    width           : 750,
                    items : [
                        Ext.create('Ext.Img', {
                            src         : 'images/sites/yandex.gif',
                            width : 40
                        }),
                        {
                            xtype       : 'text',
                            width       : 100,
                            text        : 'Яндекс:',
                            padding     : '0 0 0 10',
                        },
                        {
                            fieldLabel  : 'вкл',
                            xtype       : 'checkboxfield',
                            padding     : '0 0 0 20',
                            labelWidth  : 40,
                            name        : 'TrfYandexActive',
                            itemId      : 'TrfYandexActive',
                            inputValue  : '1'
                        },
                        {
                            fieldLabel  : 'цена',
                            //allowBlank  : false,
                            size        : 5,
                            name        : 'TrfYandex',
                            itemId      : 'TrfYandex',
                            vtype       : 'DigitsDotVtype',
                            labelWidth  : 40,
                            width       : 100,
                            xtype       : 'textfield',
                            padding     : '0 0 0 10',
                        },
                        {
                            fieldLabel  : 'xml',
                            emptyText   : 'http://',
                            name        : 'TrfYandexXmlLinks',
                            itemId      : 'TrfYandexXmlLinks',
                            padding     : '0 0 0 10',
                            readOnly    : true,
                            xtype       : 'textfield',
                            labelWidth  : 40,
                            width       : 400
                        }
                    ]
                }
            ],
            buttons : [
                {
                    text   : 'Сохранить',
                    itemId : 'PriceFormSaveBtn',
                    handler: function() {
                        SubmitPriceForm(false);

                    }
                },
                {
                    text    : 'Сохранить и закрыть',
                    itemId  : 'PriceFormSaveAndCloseBtn',
                    handler : function() {
                        SubmitPriceForm(true);

                    }
                },
                {
                    text: 'Закрыть',
                    handler: function() {
                        Ext.getCmp('AdPricesWindow').close();
                    }
                }]

        } );
        this.callParent(arguments);
    },
    listeners: {
            afterrender: {
                fn: function () {
                    var ObjectForm = Ext.getCmp('AdPricesForm');
                    ObjectForm.getForm().load({     // загружаем данные в форму
                        waitMsg : 'Открывается  ',
                        url     : 'Super.php',
                        method  : 'GET',
                        params  : {
                            Action: 'LoadAdPricesForm'
                        },
                        success: function(response, options) {
                            console.log('Цены успешно загружены');

                        }
                    });

                }
            }
    }

});



