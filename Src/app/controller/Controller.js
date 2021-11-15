Ext.define('crm.controller.Controller', {
    extend  : 'Ext.app.Controller',
    models  : [ 'KladrModel',
                'ObjectsGridModel',
                'UsersGridModel',
                'ObjectsCountryGridModel',
                'ObjectsCommerceGridModel',
                'MailGridModel',
                'OwnersGridModel',
                'RightsModel',
                'UserFormRightsModel',
                'SystemVarsModel',
                'SimpleModel'
    ],
    stores  : [ 'ObjectsGridStore',
                'UsersGridStore',
                'ObjectsCountryGridStore',
                'ObjectsCommerceGridStore',
                'ObjectDataViewStore',
                'Mail.MailGridStore',
                'ObjectForm.AvitoAltCityStore',
                'ObjectForm.KladrStore',
                'ObjectForm.DealTypeStore',
                'ObjectForm.NovoDealTypeStore',
                'ObjectForm.NovoBuildingSeriesStore',
                'ObjectForm.ObjectAgeTypeStore',
                'ObjectForm.ObjectTypeStore',
                'ObjectForm.MetroWayTypeStore',
                'ObjectForm.ObjectConditionStore',
                'ObjectForm.ToiletStore',
                'ObjectForm.WindowViewStore',
                'ObjectForm.TelephoneStore',
                'ObjectForm.GarbageStore',
                'ObjectForm.BalconStore',
                'ObjectForm.LiftStore',
                'ObjectForm.TerritoryStore',
                'ObjectForm.ParkingStore',
                'ObjectForm.BuildingTypeStore',
                'ObjectForm.MortgageStore',
                'ObjectForm.FlooringStore',
                'ObjectForm.CurrencyStore',
                'ObjectForm.ObjectPriceTypeStore',
                'ObjectForm.ObjectCountryTypeStore',
                'ObjectForm.ObjectCountryDealTypeStore',
                'ObjectForm.ObjectCountryLandTypeStore',
                'ObjectForm.ObjectCountryHighwayStore',
                'ObjectForm.ObjectCountryWaterStore',
                'ObjectForm.ObjectCountryGasStore',     // TODO стоит сократить слово 'Object'!
                'ObjectForm.ObjectCountrySewerStore',
                'ObjectForm.ObjectCountryHeatStore',
                'ObjectForm.ObjectCountryElectroStore',
                'ObjectForm.ObjectCountryPmgStore',
                'ObjectForm.ObjectCountrySecureStore',
                'ObjectForm.ObjectCountryToiletStore',
                'ObjectForm.ObjectCountryBathStore',
                'ObjectForm.ObjectCountryGarageStore',
                'ObjectForm.ObjectCountryPoolStore',
                'ObjectForm.ObjectCountryMaterialStore',
                'ObjectForm.ObjectCountryPhoneStore',
                'ObjectForm.CountryWallsTypeStore',
                'ObjectForm.ObjectCommerceDealTypeStore',
                'ObjectForm.CommerceBuildingTypeStore',
                'ObjectForm.CommerceEnterTypeStore',
                'ObjectForm.CommercePhoneLinesAddStore',
                'ObjectForm.CommerceFurnitureStore',
                'ObjectForm.CommerceParkingStore',
                'ObjectForm.CommercePricePeriodStore',
                'ObjectForm.CommercePriceTypeStore',
                'ObjectForm.CeilingHeightStore',
                'ObjectForm.CommerceRoomTypeStore',
                'ObjectForm.CommerceRoomMapStore',
                'ObjectForm.CommerceBuildingClassStore',
                'ObjectForm.CommerceCommunPayStore',
                'ObjectForm.CommerceExplutPayStore',
                'ObjectForm.CommerceConditionStore',
                'ObjectForm.CommerceBuildingStatusStore',
                'ObjectForm.CommerceVentilationStore',
                'ObjectForm.CommerceFireStore',
                'ObjectForm.CommerceHeatingStore',
                'ObjectForm.CommerceLiftsStore',
                'Owners.OwnersGridStore',
                'UserNotificationsStore',
                'StructureRulesStore',
                'UsersRightsForAdditionStore',
                'UserFormRightsStore',
                'SystemVarsStore',
                'ObjectForm.OwnerPhoneStore',
                'ClientsGridStore'

        ],
    views   : [
        'ObjectDataView',
        'ObjectsGrid',
        "ObjectsCountryGrid",

        "ObjectTabs",
        "ObjectForm",
        'ObjectPhotosTab',
        'ObjectAdditionsForm',
        'ObjectCountryAdditionsForm',
        "ObjectImageViewerWindow", "MainPanel", "ObjectWindow",
        "UsersGrid", "UsersListWindow", "UserForm", "UserWindow",//'ObjectPhotosUploadBtn',
        'Mail.MailListWindow',
        "ObjectCountryTabs",
        "ObjectCountryForm",
        "ObjectCountryWindow",
        "ObjectCountryForm",
        "ObjectCountryPhotosTab",
        'ObjectsCommerceGrid',
        'ObjectCommerceTabs',
        "ObjectCommerceWindow",
        'ObjectCommerceTabs',
        'ObjectCommerceForm',
        'ObjectCommercePhotosTab',
        'ObjectCommerceAdditionsForm',
        'Owners.OwnersGrid',
        'Settings.SettingsWindow',
        'Settings.SettingsForm',
        'Settings.StructureWindow',
        'Settings.StructureTabs',
        'Settings.StructurePositionsForm',
        'Settings.StructureGroupsForm',
        'Settings.StructureStatusesForm',
        'DatePickerWindow',
        'WelcomeWindow',
        'Settings.AdPricesWindow',
        'Settings.AdPricesForm',
        'ClientsListWindow',
        'ClientsGrid',
        'ClientWindow',
        //'ClientTabs',
        'ClientForm',
        'ObjectHistory'

        ],//'test', 'ObjectPhotosTab',
    init: function() {
        this.control({
            'viewport': {
                render: this.onRender_viewport
            },
            /*'viewport > panel': {
                //render: this.testevent
            },*/
            'SettingsForm': {
                render: this.onLoad_SettingsForm
            },
            'OwnersGrid' : {
                //render : this.setOwnersGridCountLabel2 //afterrender
                click: this.testevent
            },
            'ObjectsCountryGrid' :
            {
                //click : this.testevent
            },
            'ObjectForm': {
                //afterrender: this.InitRights
            },
            'WelcomeWindow': {
                render: this.onRenderWelcomeWindow
            },
            'UsersGrid': {  // проверить кнопку на создание сотрудника
                render: this.InitRights
            }
            /*'BtnSettings' : {
                render : this.testevent
            }*/

        });
        this.listen({

            store: {
                '#ObjectsGridStore': {
                    load: this.onStoreLoad_ObjectsVtorCountLabel
                },
                '#ObjectsCountryGridStore': {
                    load: this.onStoreLoad_ObjectsCountryCountLabel
                },
                '#Owners.OwnersGridStore': {
                    load: this.onStoreLoad_OwnersGridCountLabel
                },
                '#UsersGridStore': {
                    load: this.onStoreLoad_UsersListCountLabel // для ситуативного показа счетчика autoload в store должен быть false
                },
                '#ClientsGridStore': {
                    load: this.onStoreLoad_ClientsListCountLabel // для ситуативного показа счетчика autoload в store должен быть false
                }
            }
        });
    },
    testevent: function() {
        alert('testevent');
    },
    InitRights: function() {
        InitUserAccessRights();
    },
    onRenderWelcomeWindow: function() {
        WelcomeWindowRender();
    },
    onLoad_SettingsForm: function() {
        LoadSettingsForm();
    },
    onStoreLoad_ObjectsVtorCountLabel: function() {
        Ext.ComponentQuery.query('#ObjectsVtorCountLabel')[0].setText( 'Всего объектов: ' + Ext.data.StoreManager.lookup('ObjectsGridStore').getTotalCount() )
    },
    onStoreLoad_ObjectsCountryCountLabel: function() {
        Ext.ComponentQuery.query('#ObjectsCountryCountLabel')[0].setText( 'Всего объектов: ' + Ext.data.StoreManager.lookup('ObjectsCountryGridStore').getTotalCount() )
    },
    onStoreLoad_OwnersGridCountLabel: function() {
        Ext.ComponentQuery.query('#OwnersGridCountLabel')[0].setText( 'Всего объектов: ' + Ext.data.StoreManager.lookup('Owners.OwnersGridStore').getTotalCount() );

        if( typeof Ext.data.StoreManager.lookup('Owners.OwnersGridStore').proxy.reader.jsonData.AllowXlsExport !== "undefined" &&
            Ext.data.StoreManager.lookup('Owners.OwnersGridStore').proxy.reader.jsonData.AllowXlsExport == true) {
                // разрешить или нет экспорт собов в эксель
                Ext.ComponentQuery.query('#OwnersGridExportToExcelBtn')[0].setVisible(true);
        } else {
                Ext.ComponentQuery.query('#OwnersGridExportToExcelBtn')[0].setVisible(false);
        }

    },
    onStoreLoad_UsersListCountLabel: function() {
        Ext.ComponentQuery.query('#UsersListCountLabel')[0].setText( 'Всего сотрудников: ' + Ext.data.StoreManager.lookup('UsersGridStore').getTotalCount() );
    },
    onStoreLoad_ClientsListCountLabel: function() {
        Ext.ComponentQuery.query('#ClientsListCountLabel')[0].setText( 'Всего клиентов: ' + Ext.data.StoreManager.lookup('ClientsGridStore').getTotalCount() );
    },

    onRender_viewport: function() {

        Op_ExecAfterWork(
            // автозапуск, срабатывает после app.js: launch: GetFullUserInfo()
            function() {
                crm.view.WelcomeWindow.create({});
                //OpenOwnersGrid();

                //Ext.getCmp('WelcomeWindow').close();

                InitUserAccessRights();
                Ext.ComponentQuery.query('#Lbl_LoggedUserName')[0].setText(GlobVars.CurrentUser.FirstName+' '+GlobVars.CurrentUser.LastName);

                //OpenOwnersGrid();
            }
        );


    }
});


/*
вызов внутренних itemId
 Now you can access the above components by their unique names as below
 var pictureSaveButton = Ext.ComponentQuery.query('#picturetoolbar > #savebutton')[0];
 var orderSaveButton = Ext.ComponentQuery.query('#ordertoolbar > #savebutton')[0];
 // assuming we have a reference to the “picturetoolbar” as picToolbar
 picToolbar.down(‘#savebutton’);
 */