
    function InitUserAccessRights() {
        console.log('сверяем правила')
        if(CheckUserAccessRule('SystemSettings-Manage')) {
            Ext.ComponentQuery.query('#Btn_SysSettings')[0].setDisabled(false);
        }
        if(CheckUserAccessRule('Sob-Use')) {
            Ext.ComponentQuery.query('#Btn_Sobs')[0].setDisabled(false);
        }
        if(CheckUserAccessRule('Users-All-ReadEditDeleteRestore') ||
           CheckUserAccessRule('Users-All-Read')) {
            Ext.ComponentQuery.query('#Btn_Users')[0].setDisabled(false);
        }
        if(CheckUserAccessRule('SystemStructureAndRules-Manage')) {
            Ext.ComponentQuery.query('#Btn_StructureRights')[0].setDisabled(false);
        }

        if(CheckUserAccessRule('Advert-All-Prices')) {
            Ext.ComponentQuery.query('#Btn_AdPrices')[0].setDisabled(false);
        }


        if(CheckUserAccessRule('Advert-All-Manage') || CheckUserAccessRule('Advert-My-Manage')) {
            AdColumnsTrigger('setVisible');
        }
        //
        if(CheckUserAccessRule('Objects-All-Create')) {
            if(typeof Ext.ComponentQuery.query('#Btn_CreateObject')[0] !== "undefined" ) {
                Ext.ComponentQuery.query('#Btn_CreateObject')[0].setDisabled(false); // кнопка на все типы объектов с одним названием
            }
        }


        if(typeof Ext.ComponentQuery.query('#Btn_CreateUser')[0] !== "undefined" ) { // если кнопка проявилась
            if (CheckUserAccessRule('Users-All-Create') || CheckUserAccessRule('Users-All-ReadEditDeleteRestore')) {
                Ext.ComponentQuery.query('#Btn_CreateUser')[0].setDisabled(false);
            }
        }
        if(CheckUserAccessRule('Objects-All-Manage')) {
            // все поля уже открыты - у меня админ права
            var ObjectForm = Ext.getCmp('ObjectForm');
            if(typeof ObjectForm !== "undefined" ) {

            }
        } else if(CheckUserAccessRule('Objects-My-Manage')) {
            // Редактирование только своих объектов
            var ObjectForm  = Ext.getCmp('ObjectForm');
            if(typeof ObjectForm == "undefined") {
                var ObjectForm  = Ext.getCmp('ObjectCountryForm'); }
            if(typeof ObjectForm == "undefined") {
                var ObjectForm  = Ext.getCmp('ObjectCommerceForm'); }


            if(typeof ObjectForm !== "undefined") {
                var OwnerUserId = Ext.ComponentQuery.query('#OwnerUserId', ObjectForm)[0].value;
                if(typeof OwnerUserId !== "undefined") {
                    if( OwnerUserId == GlobVars.CurrentUser.id ) {
                        // я хозяин, открываем все поля (хотя они уже открыты)
                        //Ext.Array.each(FormItems, function(name, index, countriesItSelf) { name.enable() });
                    } else {
                        DisableObjectEdit(ObjectForm);
                    }
                }
            }
        } else if(CheckUserAccessRule('Objects-My-EditSpecial')) {
            var ObjectForm  = Ext.getCmp('ObjectForm');
            if(typeof ObjectForm == "undefined") {
                var ObjectForm  = Ext.getCmp('ObjectCountryForm'); }
            if(typeof ObjectForm == "undefined") {
                var ObjectForm  = Ext.getCmp('ObjectCommerceForm'); }


            if(typeof ObjectForm !== "undefined") {
                var OwnerUserId = Ext.ComponentQuery.query('#OwnerUserId', ObjectForm)[0].value;
                if( OwnerUserId == GlobVars.CurrentUser.id ) {
                    // в моем объекте можно менять некоторые поля
                    DisableObjectEditWithException(Ext.getCmp('ObjectForm')); // сработает там где компонент существует
                    DisableObjectEditWithException(Ext.getCmp('ObjectCountryForm')); //
                    DisableObjectEditWithException(Ext.getCmp('ObjectCommerceForm'));
                } else {
                    // в чужих объектах нельзя ничего менять
                    DisableObjectEdit(Ext.getCmp('ObjectForm')); // сработает там где компонент существует
                    DisableObjectEdit(Ext.getCmp('ObjectCountryForm')); //
                    DisableObjectEdit(Ext.getCmp('ObjectCommerceForm'));
                }
            }

        } else {
            DisableObjectEdit(Ext.getCmp('ObjectForm')); // сработает там где компонент существует
            DisableObjectEdit(Ext.getCmp('ObjectCountryForm')); //
            DisableObjectEdit(Ext.getCmp('ObjectCommerceForm'));
        }


    }

    function DisableObjectEdit(ObjectForm) {
        //запретить редактирование во всех вкладках объекта
        if(typeof ObjectForm !== "undefined") {
            var FormItems = ObjectForm.items.getRange();
            Ext.Array.each(FormItems, function(name, index) { name.disable() }); // блокируем все поля
            ObjectForm.queryById('ObjectFormSaveBtn').setVisible(false);         // скрываем кнопки во вкладке "Характеристики"
            ObjectForm.queryById('ObjectFormSaveAndCloseBtn').setVisible(false);

            var PhotosTab = Ext.getCmp('ObjectPhotosTab');
            if(typeof PhotosTab == "undefined") {
                var PhotosTab = Ext.getCmp('ObjectCountryPhotosTab'); }
            if(typeof PhotosTab == "undefined") {
                var PhotosTab = Ext.getCmp('ObjectCommercePhotosTab'); }

            PhotosTab.queryById('ObjectUploadButton').setVisible(false);
            PhotosTab.queryById('ObjectDataView').setDisabled(true);

            var AddObjectForm = Ext.getCmp('ObjectAdditionsForm');
            if(typeof AddObjectForm == "undefined") {
                var AddObjectForm = Ext.getCmp('ObjectCountryAdditionsForm'); }

            if(typeof AddObjectForm == "undefined") {
                var AddObjectForm = Ext.getCmp('ObjectCommerceAdditionsForm'); }

            var AddFormItems = AddObjectForm.items.getRange();
            Ext.Array.each(AddFormItems, function(name, index) { name.disable() }); // блокируем все поля в форме "дополнительно"
            AddObjectForm.queryById('ObjectFormSaveBtn').setVisible(false);                       // скрываем кнопки во вкладке "Дополнительно"
            AddObjectForm.queryById('ObjectFormSaveAndCloseBtn').setVisible(false);
        }
    }

    function DisableObjectEditWithException(ObjectForm) {
        //запретить редактирование во всех вкладках объекта
        if(typeof ObjectForm !== "undefined") {
            var FormItems = ObjectForm.items.getRange();
            Ext.Array.each(FormItems, function(name, index) {
                name.disable();// блокируем все поля
            });
            ObjectForm.queryById('PriceAgentContainer').enable();// открываем избранные
            ObjectForm.queryById('AgentContainer').disable();
            ObjectForm.queryById('Description').enable();
            ObjectForm.queryById('GeoWinBtn').enable();
            ObjectForm.queryById('Action').enable();
            ObjectForm.queryById('PositionType').enable();
            ObjectForm.queryById('LoadedObjectId').enable();
            ObjectForm.queryById('EditSpecial').enable();
            ObjectForm.queryById('EditSpecial').setValue('1'); // помечаем тип сохранения - только некоторые поля
            //ObjectForm.queryById('Latitude').enable();
            //ObjectForm.queryById('YandexAddress').enable();
            //ObjectForm.queryById('EditSpecial').enable();
            //ObjectForm.queryById('').enable();


            //ObjectForm.queryById('ObjectFormSaveBtn').setVisible(false);         // скрываем кнопки во вкладке "Характеристики"
            //ObjectForm.queryById('ObjectFormSaveAndCloseBtn').setVisible(false);

            /*var PhotosTab = Ext.getCmp('ObjectPhotosTab');
            if(typeof PhotosTab == "undefined") {
                var PhotosTab = Ext.getCmp('ObjectCountryPhotosTab'); }
            if(typeof PhotosTab == "undefined") {
                var PhotosTab = Ext.getCmp('ObjectCommercePhotosTab'); }

            PhotosTab.queryById('ObjectUploadButton').setVisible(false);
            PhotosTab.queryById('ObjectDataView').setDisabled(true);*/

            var AddObjectForm = Ext.getCmp('ObjectAdditionsForm');
            if(typeof AddObjectForm == "undefined") {
                var AddObjectForm = Ext.getCmp('ObjectCountryAdditionsForm'); }

            if(typeof AddObjectForm == "undefined") {
                var AddObjectForm = Ext.getCmp('ObjectCommerceAdditionsForm'); }

            var AddFormItems = AddObjectForm.items.getRange();
            Ext.Array.each(AddFormItems, function(name, index) { name.disable() }); // блокируем все поля в форме "дополнительно"
            AddObjectForm.queryById('ObjectFormSaveBtn').setVisible(false);                       // скрываем кнопки во вкладке "Дополнительно"
            AddObjectForm.queryById('ObjectFormSaveAndCloseBtn').setVisible(false);
        }
    }


    function GetFullUserInfo() {
        // Загрузить все данные текущего пользователя
        // Исполняется при входе в систему.
        Op_Start(this);
        Ext.Ajax.request({
            url : GetFullUserInfoUrl,
            /*params  : {
                a  : 'b'
            },*/
            success: function(response, opts) {
                var result = Ext.decode(response.responseText);
                //console.log(GlobVars);
                GlobVars.CurrentUser     = result;  // выводим переменные пользователя в глоб перем
                //console.log(GlobVars);
                Op_Stop();
            },
            failure: function(response, opts) {
                var msg = 'Невозможно определить права пользователя';
                alert(msg); // TODO протоколировать!!!
                Op_ErrorStop(msg);
            }
        });
    }

    function GetSysParams() {
        // Загрузить доступные параметры из тбл SysParams
        // Исполняется при входе в систему в app.js
        Op_Start(this);
        Ext.Ajax.request({
            url : MainAjaxDriver + '?Action=GetSysParams',
            success: function(response, opts) {
                var result = Ext.decode(response.responseText);
                GlobVars.SysParams     = result;
                Op_Stop();
            },
            failure: function(response, opts) {
                var msg = 'Невозможно взять системные параметры';
                alert(msg); // TODO протоколировать!!!
                Op_ErrorStop(msg);
            }
        });
    }

    function CheckUserAccessRule(RuleName) {
        var out = null;

        //if(typeof GlobVars.CurrentUser.UserAccessRules[RuleName] !== "undefined"){}
            if(typeof GlobVars.CurrentUser.UserAccessRules[RuleName] !== "undefined" &&
                      GlobVars.CurrentUser.UserAccessRules[RuleName] === true) {
                out = true;
            } else {
                out = false;
                //console.log(GlobVars.CurrentUser.UserAccessRules);
                //console.log(Words_AccessRules_DenyRule + RuleName + '. '+Words_AccessRules_AskBoss);
            }
        //}
        return out;
    }

