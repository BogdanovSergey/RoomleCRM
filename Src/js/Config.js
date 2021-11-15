Ext.Loader.setConfig({
        enabled: true,
        paths: {
            'Ext.ux': 'ext-4.2.1.883/examples/ux',
            'Ext.ux.upload': 'ext-4.2.1.883/src/ux/upload/'
        }
    });
Ext.Loader.setPath('Ext.ux', 'ext-4.2.1.883/examples/ux', 'Ext.ux.DataView', '../ux/DataView/');
Ext.require([
    'Ext.data.*',
    'Ext.util.*',
    'Ext.state.*',
    'Ext.form.*',
    'Ext.grid.*',
    'Ext.tree.*',
    'Ext.view.View',
    'Ext.container.Viewport',
    'Ext.ux.layout.*',
    'Ext.ux.form.*',
    'Ext.ux.CheckColumn',
    'Ext.ux.DataView.DragSelector',
    'Ext.ux.DataView.LabelEditor',
    'Ext.ux.data.PagingMemoryProxy',
    'Ext.ux.upload.Button',
    'Ext.ux.upload.plugin.Window',
    'Ext.tip.QuickTipManager',
    'Ext.window.Window'
]);

/* MainSiteUrl = ''; // см. index.html шаблон */
// Init the singleton.  Any tag-based quick tips will start working.
Ext.tip.QuickTipManager.init();

// Apply a set of config properties to the singleton
Ext.apply(Ext.tip.QuickTipManager.getQuickTip(), {
    maxWidth: 200,
    minWidth: 100,
    showDelay: 5000,      // Show 50ms after entering target
    style: {
        backgroundColor:'#ff0000'
    }
});

// TODO слэши в переменных поменять наоборот
ImagesUploadUrl         = 'Super.php?Action=UploadFiles';

GetObjectImagesUrl      = 'Super.php?Action=GetObjectImages&ObjectId=';         // TODO перенести ObjectId внутрь, а отсюда убрать
LoadObjectImageUrl      = 'Super.php?Action=LoadObjectImageByImageId&ImageId=';
DeleteObjectImageUrl    = 'Super.php?Action=DeleteObjectImageByImageId';
SetObjectFirstImageUrl  = 'Super.php?Action=SetObjectFirstImage';
ArchivateObjectUrl      = 'Super.php?Action=ArchivateObjectById';
RestoreObjectUrl        = 'Super.php?Action=RestoreObjectById';
AjaxUrl                 = MainSiteUrl + 'Super.php?Action=GetObjectFormParams';
UpdateAdTarifUrl        = 'Super.php?Action=UpdateAdTarifObjectState';
QuickObjectQueryById    = 'Super.php?Action=QuickObjectQueryById';

QuickObjectQueryById    = 'Super.php?Action=QuickObjectQueryById';

MainAjaxDriver          = MainSiteUrl + 'Super.php'; // ссылка на главный файл-ajax-обработчик
GetFullUserInfoUrl      = MainAjaxDriver + '?Action=GetFullUserInfo';
// Access rules
GetAccessRulesStructureUrl      = MainAjaxDriver + '?Action=GetAccessRulesStructure';
GetAccessRulesForAdditionUrl    = MainAjaxDriver + '?Action=GetAccessRulesForAddition';
AddAccessRuleIdForUserIdUrl     = MainAjaxDriver + '?Action=AddAccessRuleIdForUserId';
DeleteAccessRuleIdForUserIdUrl  = MainAjaxDriver + '?Action=DeleteAccessRuleIdForUserId';
AddNewPositionUrl               = MainAjaxDriver + '?Action=AddNewPosition';
AddNewGroupUrl                  = MainAjaxDriver + '?Action=AddNewGroup';
AddNewStatusUrl                 = MainAjaxDriver + '?Action=AddNewStatus';
RemoveStructureItemUrl          = MainAjaxDriver + '?Action=RemoveStructureItem';
AttachRuleToItemUrl             = MainAjaxDriver + '?Action=AttachRuleToItem';
RenameStrucItemUrl              = MainAjaxDriver + '?Action=RenameStrucItem';
DataRequestUrl                  = MainAjaxDriver + '?Action=DataRequest';

// Настройки для функций с гридами
CityObjectsGrid_Action          = 'GetObjectsList';
CityObjectsGrid_Active          = 1;     // переменные для текущего открытого грида по городскому типу
CityObjectsGrid_OnlyUserId      = '';

CountryObjectsGrid_Action       = 'GetCountryObjectsList';
CountryObjectsGrid_Active       = 1;     // переменные для текущего открытого грида по загородке
CountryObjectsGrid_OnlyUserId   = '';

CommerceObjectsGrid_Action      = 'GetCommerceObjectsList';
CommerceObjectsGrid_Active      = 1;
CommerceObjectsGrid_OnlyUserId  = '';

UsersGrid_Action          = 'GetUsersList';
UsersGrid_Active          = 1;     // по-умолчанию показываем активных
UsersGrid_OnlyUserId      = ''; // удалить?

/*CityObjectsGrid_Proxy =  { type    : 'ajax',
                           Params  : '',
                           url     : '',
                           reader  : { type: 'json',
                                        root: 'data',
                                        totalProperty: 'total' }
                           };*/
// кнопка установки фильтра на агента, значения по-умолчанию
FilterOwnerUserSelect_Action = 'GetObjectFormParams';
FilterOwnerUserSelect_ActiveObjects = 1;
FilterOwnerUserSelect_GetAgents = 1;
FilterOwnerUserSelect_OnlyFio   = 1;
FilterOwnerUserSelect_WithSumm  = 1;
FilterOwnerUserSelect_RealtyType = ''; // подтверждается в ините каждого грида, меняется на 'country' и тд
FilterOwnerUserSelect_Proxy = { type    : 'ajax',
                                Params  : '',
                                //url     : '',
                                reader  : { type: 'json',
                                            root: 'data',
                                            totalProperty: 'total' }
                                };
//
ActiveCityObjectsGridProxyParams ='Action=GetObjectsList&Active=1'; // специально выведено для гибкости
ActiveCityObjectsGridProxy =  { type    : 'ajax',
                                Params  : ActiveCityObjectsGridProxyParams,
                                url     : MainAjaxDriver + '?' + ActiveCityObjectsGridProxyParams,
                                reader  : { type: 'json',
                                            root: 'data',
                                            totalProperty: 'total' }
                               };
ArchivedCityObjectsGridProxyParams = 'Action=GetObjectsList&Active=0';
ArchivedCityObjectsGridProxy = {
                                type    : 'ajax',
                                Params  : ArchivedCityObjectsGridProxyParams,
                                url     : MainAjaxDriver + '?' + ArchivedCityObjectsGridProxyParams,
                                reader  : { type    : 'json',
                                            root    : 'data',
                                            totalProperty: 'total' }
                                };
ActiveCountryObjectsGridProxyParams = 'Action=GetCountryObjectsList&Active=1';
ActiveCountryObjectsGridProxy = {
                                type    : 'ajax',
                                Params  : ActiveCountryObjectsGridProxyParams,
                                url     : MainAjaxDriver + '?' + ActiveCountryObjectsGridProxyParams,
                                reader  : {  type          : 'json',
                                             root          : 'data',
                                             totalProperty : 'total' } };

ArchivedCountryObjectsGridProxyParams = 'Action=GetCountryObjectsList&Active=0';
ArchivedCountryObjectsGridProxy = {
                                type   : 'ajax',
                                Params : ArchivedCountryObjectsGridProxyParams,
                                url    : MainAjaxDriver + '?' + ArchivedCountryObjectsGridProxyParams,
                                reader : {  type            : 'json',
                                            root            : 'data',
                                            totalProperty   : 'total' } };

ActiveCommerceObjectsGridProxyParams ='Action=GetCommerceObjectsList&Active=1'; // специально выведено для гибкости
ActiveCommerceObjectsGridProxy =  { type    : 'ajax',
    Params  : ActiveCommerceObjectsGridProxyParams,
    url     : MainAjaxDriver + '?' + ActiveCommerceObjectsGridProxyParams,
    reader  : { type: 'json',
        root: 'data',
        totalProperty: 'total' }
};

ActiveMailGridProxyParams = 'Action=GetMailList&Active=1';
ActiveMailGridProxy = { type    : 'ajax',
                        Params  : ActiveMailGridProxyParams,
                        url     : MainAjaxDriver + '?'+ActiveMailGridProxyParams,
                        reader  : { type: 'json',
                            root: 'data',
                            totalProperty: 'total' }
};

// Собственники
OwnersGridProxyParams = 'Action=LoadJsonSobList';
OwnersGridProxy = { type    : 'ajax',
                    Params  : OwnersGridProxyParams,
                    url     : MainAjaxDriver + '?' + OwnersGridProxyParams,
                    reader  : { type: 'json',
                        root: 'data',
                        totalProperty: 'total' },
                    extraParams   : {
                        ChosenDate : ''
                    }
                };

OwnersGetObject = MainAjaxDriver + '?' + 'Action=OwnersGetObject';
OwnersSaveComment = MainAjaxDriver + '?' + 'Action=OwnersSaveComment';
OwnersGetComments = MainAjaxDriver + '?' + 'Action=OwnersGetComments';

GetObjectOwnerPhonesUrl = 'Super.php?Action=GetObjectFormParams';
////////

ActiveUsersGridProxy    = { type   : 'ajax', url : MainAjaxDriver + '?Action=GetUsersList&Active=1',
    reader : { type: 'json', root: 'data', totalProperty: 'total' } };
ArchivedUsersGridProxy  = { type   : 'ajax', url : MainAjaxDriver + '?Action=GetUsersList&Active=0',
    reader : { type: 'json', root: 'data', totalProperty: 'total' } };
afafafaf    = { type   : 'ajax', url : MainAjaxDriver + '?Action=GetAccessRulesForAddition&Active=1',
    reader : { type: 'json', root: 'data', totalProperty: 'total' } };
ArchivateUserUrl        = MainAjaxDriver + '?Action=ArchivateUserById';
RestoreUserUrl          = MainAjaxDriver + '?Action=RestoreUserById';
GetAvitoCitiesArrByRegionUrl        = MainAjaxDriver + '?Action=GetAvitoCitiesArrByRegion';
CheckCityInRegionUrl    = MainAjaxDriver + '?Action=CheckCityInRegionInAvitoLib';

LoadObjectHistoryUrl    = MainAjaxDriver + '?Action=LoadObjectHistory';

ArchivateClientUrl        = MainAjaxDriver + '?Action=ArchivateClientById';
RestoreClientUrl          = MainAjaxDriver + '?Action=RestoreClientById';

ActiveClientsGridProxy      = { type   : 'ajax', url : MainAjaxDriver + '?Action=GetClientsList&Active=1',
                                reader : { type: 'json', root: 'data', totalProperty: 'total' }
};
ArchivedClientsGridProxy    = { type   : 'ajax', url : MainAjaxDriver + '?Action=GetClientsList&Active=0',
                                reader : { type: 'json', root: 'data', totalProperty: 'total' }
};
ClientsGrid_Action          = 'GetClientsList';
ClientsGrid_Active          = 1;

//UsersRightsForAdditionProxy = { type   : 'ajax', url : MainAjaxDriver + '?Action=GetUsersRightsForAddition',
//    reader : { type: 'json', root: 'data', totalProperty: 'total' } };

OpenMailUrl      = 'Super.php?Action=OpenMailById';

///////////// Words
Words_CreateObjectTitle         = 'Форма добавления объекта городской недвижимости';
Words_CreateCountryObjectTitle  = 'Форма добавления объекта загородной недвижимости';
Words_CreateCommerceObjectTitle = 'Форма добавления объекта коммерческой недвижимости';
Words_EditObjectTitle           = 'Форма редактирования объекта';

Words_CreateUserTitle = 'Форма добавления сотрудника';
Words_EditUserTitle   = 'Форма редактирования сотрудника';

Words_CreateClientTitle = 'Форма добавления клиента';
Words_EditClientTitle   = 'Форма редактирования клиента';

Words_SystemErrorMsg    = 'Произошла системная ошибка №';
Words_CallProgrammerMsg = 'Пожалуйста, свяжитесь с программистом: +7(903)124-55-31';

Words_AvitoCityNotFound = 'Населенный пункт не обнаружен на некоторых рекламных порталах!<br>';
Words_AvitoCityChoose   = 'Пожалуйста, укажите ближайший к вашему объекту нас. пункт из списка';
Words_AvitoCityChoosed  = 'Ближайший к объекту населеный пункт';

Words_ObjectRealtyTypeChanged = 'При изменении типа недвижимости будьте внимательны, некоторые поля могут отражаться некорректно по причине отсутствия данных.';

Words_AccessRules_DenyRule = 'У пользователя нет прав на выполнение ';
Words_AccessRules_AskBoss  = 'Обратитесь к вашему руководителю';
Words_NoRules              = 'У вас недостаточно прав для выполнения этой операции';
Words_PaymentTitle  =   "Два способа оплаты";
Words_PaymentMsg    =   '1. На Яндекс кошелек: 410013299893823<br>' +
                        '<a href="https://money.yandex.ru/to/410013299893823" target="_blank">Оплата через сайт Яндекс.Деньги</a><br><br>'+
                        '2. Через банкомат, на карту MasterCard: 5469380062459028<br><br><br><br>'+
                        'Благодарим вас за своевременную оплату!';

/*
    описание ошибок:

100 - запрос на удаление фотки объекта
101 - ошибка при удалении фотки объекта
 102 - запрос на удаление/восстановление объекта
 103 - ошибка при удалении/восстановлении объекта
 104 - при обновлении выгрузки на портал
 105 - ошибка запроса --//--

 106 - запрос на удаление пользователя
 107 - запрос на восстановление пользователя
 108
 109, 110 - запрос на прочтение почты
 111 - ошибка при Logout'e
 */


Ext.state.Manager.setProvider(new Ext.state.CookieProvider({
    // включаем setProvider - драйвер сохраняющий пользовательские настройки (ширина, сортировка гридов), в компоненте еще нужны: stateId,stateful,stateEvents
    // TODO сделать кнопку в интерфейсе по сбросу этих настроек
    expires: new Date(new Date().getTime()+(1000*60*60*24*365)) //365 days from now
}));



// тип фильтрации поля - цифры
var dig = /^\d+/;
Ext.apply(Ext.form.field.VTypes, {
    DigitsVtype: function(val, field) {
        return dig.test(val);
    },
    DigitsVtypeText: 'только цифры',
    DigitsVtypeMask: /[\d]/i
});


// тип фильтрации поля - цифры и точка
// TODO позволить набирать только \d+[.\d+]
var digDot = /^\d+\.?/;
Ext.apply(Ext.form.field.VTypes, {
    DigitsDotVtype: function(val, field) {
        return digDot.test(val);
    },
    DigitsDotVtypeText: 'только цифры и точка',
    DigitsDotVtypeMask: /[\d\.]/i
});


Ext.selection.Model.override({
    // hack for buffered store bug
    storeHasSelected: function(record) {
        //console.log(this.store);
        var store = this.store,
            records,
            len, id, i;
        if (record.hasId() && /*store.getById(record)*/ store.getById(record.getId())) {
            return true;
        } else {
            records = store.data.items;
            //len = records.length;
            len = records ? records.length : 0;
            id = record.internalId;
            for (i = 0; i < len; ++i) {
                if (id === records[i].internalId) {
                    return true;
                }
            }
        }
        return false;
    }
});

// глобальные переменные
OpenedObjectsGrid = '';     // сократить
// GlobVars.NamesObj['commerce'].PhotosTab
GlobVars = {                // GlobVars.OpenedRealtyType
    OpenedRealtyType    : '', // тип открытого объекта city/country/commerce...
    RegionOrCityUpdated : 0, // форма объектов: 0 - адрес не редактировался, 1 - адрес был отредактирован
    //Op : {...} // назначаются в Operations.js
    //Log: {...} // назначаются в Logging.js
    SysParams : {
                            // параметры из тбл. SysParams
    },
    CurrentUser : {
        UserAccessRules : {}// данные текущего пользователя, загружаются в app/funcs/Access.js:GetFullUserInfo()
    },
    //UserAccessRules : {}, // список действий разрешенных клиенту,
    NamesObj : {            // элементы для типов недвижимости
        city : {
            PhotosTab           : 'ObjectPhotosTab',
            ObjectAdditionsForm : 'ObjectAdditionsForm'
        },
        country : {
            PhotosTab           : 'ObjectPhotosTab',
            ObjectAdditionsForm : 'ObjectCountryAdditionsForm'
        },
        commerce : {
            PhotosTab           : 'ObjectCommercePhotosTab',
            ObjectAdditionsForm : 'ObjectCommerceAdditionsForm'
        }
    },
    Design : {               // Внешние прибамбасы, дизайн, графика
        Spinner : '<img src="' + MainSiteUrl + 'images/spinner.gif" width="30">',
        Avito   :               MainSiteUrl + 'images/sites/avito.gif',
        Cian    :               MainSiteUrl + 'images/sites/cian.gif',
        Sob     :               MainSiteUrl + 'images/sites/sob.gif',
        Irr     :               MainSiteUrl + 'images/sites/irr.gif'
    },
    Temp : {
        OpenedSobObject : 0
    }
}





// необходимые библиотеки
Ext.Loader.loadScript('app/funcs/Access.js');
Ext.Loader.loadScript('app/funcs/Logging.js');
Ext.Loader.loadScript('js/Funcs.js');
Ext.Loader.loadScript('app/funcs/Advert.js');
Ext.Loader.loadScript('app/funcs/Operations.js');
Ext.Loader.loadScript('app/funcs/UserFuncs.js');
Ext.Loader.loadScript('app/funcs/ObjectsFuncs.js');
Ext.Loader.loadScript('app/funcs/OpenRealtyGrid.js');
Ext.Loader.loadScript('app/funcs/OpenObject.js');
Ext.Loader.loadScript('app/funcs/AvitoFuncs.js');
Ext.Loader.loadScript('app/funcs/OwnersFuncs.js');
Ext.Loader.loadScript('app/funcs/SobFuncs.js');
Ext.Loader.loadScript('app/funcs/SettingsFuncs.js');
Ext.Loader.loadScript('app/funcs/System.js');
Ext.Loader.loadScript('app/funcs/ClientsFuncs.js');