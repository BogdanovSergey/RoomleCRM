
function BuildGridUrlString(MainAjaxDriver, Action, Active, OnlyUserId) {
    // TODO OnlyUserId -  может быть массивом (для Multiselect)
    // делаем ссылку из глобальных переменных для обновления ГРИДов
    // TODO объединить в одну ф-ю
    var url = MainAjaxDriver + '?' +
        '&Action='      + Action +
        '&Active='      + Active +
        '&OnlyUserId='  + OnlyUserId;
    console.log( 'BuildGridUrlString(): ' + url);
    return url;
}

function BuildOwnersUrlString(MainAjaxDriver, Action, text, OwnersDate) {
    // TODO объединить в одну ф-ю
    var url = MainAjaxDriver+ '?' +
        '&Action='                 + Action +
        '&StreetSearchField='      + text +
        '&SobListNeededDate='      + OwnersDate;
    console.log( 'BuildOwnersUrlString(): ' + url);
    return url;
}

function PermitActionByObjectColor(Color) {
    out = false;
    if(Color == 'LightYellow') {
        Ext.Msg.show({
            title   :'Действие недоступно',
            msg     : 'Сначала откройте объект, проверьте правильность данных и сохраните.',
            buttons : Ext.Msg.OK,
            icon    : Ext.Msg.ERROR
        });
    } else {
        out = true;
    }
    return out;
}


function BuildFilterOwnerUserSelectUrlString(MainAjaxDriver, Action, Active, GetAgents, OnlyFio, WithSumm, RealtyType) {
    // кнопка-фильтр выбор агента
    var url = MainAjaxDriver + '?' +
        '&Action='          + Action +
        '&ActiveObjects='   + Active +
        '&Active=1'         +
        '&HideZeroUsers=1'  +
        '&GetAgents='       + GetAgents +
        '&OnlyFio='         + OnlyFio +
        '&WithSumm='        + WithSumm +
        '&RealtyType='      + RealtyType;
    console.log( 'BuildFilterOwnerUserSelectUrlString(): ' + url);
    return url;
}

function BuildObjectExportBtnUrlString(MainAjaxDriver, Action, Active, OnlyUserId, DownloadType, SortColumn, SortDir) {
    // кнопка экспорта в Excel
    var url = MainAjaxDriver + '?' +
        '&Action='      + Action +
        '&Active='      + Active +
        '&OnlyUserId='  + OnlyUserId +
        '&DownloadType='+ DownloadType;
    if(typeof SortColumn !== "undefined") { //#COLUMNSORTING// готовим параметры сортировки для совместимости с serverside сортировкой
        var SortObj         = new Object();
        SortObj.property    = SortColumn;
        SortObj.direction   = SortDir;
        url = url + '&sort='+JSON.stringify( Array(SortObj) );
    }
    console.log( arguments.callee.name + '('+Action+'): ' + url);
    return url;
}

function ChangeCountryObjectFormTrigger(ObjectType) {
    console.log('ChangeCountryObjectFormTrigger()');
    /*
     ['4', 'дом'],
     ['5', 'часть дома'],
     ['6', 'коттедж'],
     ['7', 'таунхаус'],
     ['8', 'дача'],
     ['9', 'земельный участок'],
     ['10', 'коттеджный поселок'],
     ['11', 'дуплекс'],
     ['12', 'квадрохаус'],
     ['13', 'усадьба']
     */
    if(ObjectType == 1) {
        // квартира - делаем вид по-умолчанию
        ChangeCountryObjectFormToDefaultView();
    } else if(ObjectType == 2) {
        // это доля
        ChangeCountryObjectFormToDefaultView();
        ChangeCountryObjectFormView('part', true);
    } else if(ObjectType == 3) {
        // это комната
        ChangeCountryObjectFormToDefaultView();
        ChangeCountryObjectFormView('sellroom', true);

    } else {
        // оставляем вид по-умолчанию, убираем дополнительные поля
        ChangeCountryObjectFormToDefaultView();
    }
}
function ChangeCountryObjectFormToDefaultView() {
    // выключаем все дополнительные поля
    ChangeCountryObjectFormView('sellroom', false);
    ChangeCountryObjectFormView('part', false);
}

function ChangeCountryObjectFormView(Type, Value) {
    console.log('ChangeCountryObjectFormView(....)');
    if(Type == 'sellroom') {
        console.log('ChangeCountryObjectFormView(): ' + Type + ', ' + Value);
        // обновить форму для полей по продаже комнаты
        if(Value) {
            // включить поля
            //Ext.ComponentQuery.query('#RoomsSell')[0].setVisible(true);
            //.down('textfield[name=PartsSell]');
//            var ObjectForm = Ext.widget('ObjectForm');
            Ext.ComponentQuery.query('#PartsSell')[0].hide();
            Ext.ComponentQuery.query('#PartsTotal')[0].hide();
            Ext.ComponentQuery.query('#RoomsSell')[0].show();

            Ext.apply(Ext.ComponentQuery.query('#RoomsSell')[0],     {allowBlank: false}, {});
            //Ext.getCmp('SquareLiving')
            Ext.ComponentQuery.query('#SquareLiving')[0].setFieldLabel('Жилая (продаваемых комнат)'); // TODO сделать красиво, чтобы не разъезжались поля по вертикали

        } else {
            // вЫключить поля
            //Ext.ComponentQuery.query('#RoomsSell')[0].setVisible(false);
            Ext.ComponentQuery.query('#RoomsSell')[0].hide();
            Ext.apply(Ext.ComponentQuery.query('#RoomsSell')[0],     {allowBlank: true}, {});
            //Ext.getCmp('SquareLiving').setFieldLabel('Жилая');
            Ext.ComponentQuery.query('#SquareLiving')[0].setFieldLabel('Жилая');
        }
    } else if(Type == 'part') {//console.log( Ext.getCmp('PartsSell') );
        console.log('ChangeCountryObjectFormView(): ' + Type + ', ' + Value);
        // доля
        if(Value) {
            // включить поля
            //Ext.apply(Ext.getCmp('PartsSell'),    {allowBlank: true}, {});
            //Ext.getCmp('PartsSell').setVisible(true);
            //Ext.getCmp('PartsTotal').setVisible(true);
            Ext.ComponentQuery.query('#PartsSell')[0].show();
            Ext.ComponentQuery.query('#PartsTotal')[0].show();
            Ext.apply(Ext.ComponentQuery.query('#PartsSell')[0],     {allowBlank: false}, {}); // становится обязательным к заполнению
            Ext.apply(Ext.ComponentQuery.query('#PartsTotal')[0],    {allowBlank: false}, {}); // становится обязательным к заполнению
        } else {
            // вЫключить поля
            //Ext.getCmp('PartsSell').setVisible(false);
            //Ext.getCmp('PartsTotal').setVisible(false);
            Ext.ComponentQuery.query('#PartsSell')[0].hide();
            Ext.ComponentQuery.query('#PartsTotal')[0].hide();
            Ext.apply(Ext.ComponentQuery.query('#PartsSell')[0],     {allowBlank: true}, {}); // НЕобязательно к заполнению
            Ext.apply(Ext.ComponentQuery.query('#PartsTotal')[0],    {allowBlank: true}, {}); // НЕобязательно к заполнению
        }
    } else {

    }
}


function ChangeObjectFormTrigger(RealtyType, ItemType) {
    // Общий "триггер" на открытие/изменение поля типа объекта (комната/доля, земля/коттедж):
    // Например что-то должно быть открыто/закрыто, обязательно/необязательно, в зависимости от типа объекта
    console.log(new Date() + ' '+arguments.callee.name + '('+RealtyType+', '+ItemType+')');
    if(RealtyType == 'city') {
        if(ItemType == 1) {
            // квартира - делаем вид по-умолчанию
            ChangeObjectFormToDefaultView();
        } else if(ItemType == 2) {
            // это доля
            ChangeObjectFormToDefaultView();
            ChangeObjectFormView('part', true);
        } else if(ItemType == 3) {
            // это комната
            ChangeObjectFormToDefaultView();
            ChangeObjectFormView('sellroom', true);


        } else {
            // оставляем вид по-умолчанию, убираем дополнительные поля
            ChangeObjectFormToDefaultView();
        }
    } else if(RealtyType == 'country') {
        if(ItemType == 9 || ItemType == 10) {
            // 9 - земельный участок
            // 10- коттеджный поселок
            //alert("ChangeObjectFormTrigger("+ItemType+")");

            Ext.apply(Ext.ComponentQuery.query('#CountryWallsTypeId')[0], {allowBlank: true}, {});
            Ext.ComponentQuery.query('#CountryWallsTypeId')[0].clearInvalid();
        } else {
            // Это дома, материал стен обязателен для Авито
            Ext.apply(Ext.ComponentQuery.query('#CountryWallsTypeId')[0], {allowBlank: false}, {});
        }

    } else if(RealtyType == 'commerce') {

    } else {
        alert("ChangeObjectFormTrigger("+ItemType+")");
    }
}

function ChangeObjectAgeTypeFormTrigger( ObjectParamsId) {

    if(ObjectParamsId == 57) {
        //ObjectAgeType = ['57', 'новостройка']]
        if(typeof Ext.ComponentQuery.query('#NovoDealType')[0] !== "undefined") {
            // эти поля есть только во вторичке, а ф-я запускается и из загородки
            Ext.ComponentQuery.query('#NovoDealType')[0].show();
            Ext.apply(Ext.ComponentQuery.query('#NovoDealType')[0], {allowBlank: false}, {});
            Ext.ComponentQuery.query('#NovoParams')[0].show();
        }
        Ext.ComponentQuery.query('#DealType')[0].hide();
        Ext.apply(Ext.ComponentQuery.query('#DealType')[0], {allowBlank: true}, {});



    } else {
        // ObjectAgeType = ['56', 'вторичка'],
        if(typeof Ext.ComponentQuery.query('#NovoDealType')[0] !== "undefined") {
            Ext.ComponentQuery.query('#NovoDealType')[0].hide();
            Ext.apply(Ext.ComponentQuery.query('#NovoDealType')[0], {allowBlank: true}, {});
            Ext.ComponentQuery.query('#NovoParams')[0].hide();
        }
        Ext.ComponentQuery.query('#DealType')[0].show();
        Ext.apply(Ext.ComponentQuery.query('#DealType')[0], {allowBlank: false}, {});
    }
}


function ChangeObjectFormToDefaultView() {
    // выключаем все дополнительные поля
    ChangeObjectFormView('sellroom', false);
    ChangeObjectFormView('part', false);
}

function ChangeObjectFormView(Type, Value) {
    console.log('ChangeObjectFormView(....)');
    if(Type == 'sellroom') {
        console.log('ChangeObjectFormView(): ' + Type + ', ' + Value);
        // обновить форму для полей по продаже комнаты
        if(Value) {
            // включить поля
            //Ext.ComponentQuery.query('#RoomsSell')[0].setVisible(true);
            //.down('textfield[name=PartsSell]');
//            var ObjectForm = Ext.widget('ObjectForm');
            Ext.ComponentQuery.query('#PartsSell')[0].hide();
            Ext.ComponentQuery.query('#PartsTotal')[0].hide();
            Ext.ComponentQuery.query('#RoomsSell')[0].show();

            Ext.apply(Ext.ComponentQuery.query('#RoomsSell')[0],     {allowBlank: false}, {});
            //Ext.getCmp('SquareLiving')
            Ext.ComponentQuery.query('#SquareLiving')[0].setFieldLabel('Жилая (продаваемых комнат)'); // TODO сделать красиво, чтобы не разъезжались поля по вертикали

        } else {
            // вЫключить поля
            //Ext.ComponentQuery.query('#RoomsSell')[0].setVisible(false);
            Ext.ComponentQuery.query('#RoomsSell')[0].hide();
            Ext.apply(Ext.ComponentQuery.query('#RoomsSell')[0],     {allowBlank: true}, {});
            //Ext.getCmp('SquareLiving').setFieldLabel('Жилая');
            Ext.ComponentQuery.query('#SquareLiving')[0].setFieldLabel('Жилая');
        }
    } else if(Type == 'part') {//console.log( Ext.getCmp('PartsSell') );
        console.log('ChangeObjectFormView(): ' + Type + ', ' + Value);
        // доля
        if(Value) {
            // включить поля
            //Ext.apply(Ext.getCmp('PartsSell'),    {allowBlank: true}, {});
            //Ext.getCmp('PartsSell').setVisible(true);
            //Ext.getCmp('PartsTotal').setVisible(true);
            Ext.ComponentQuery.query('#PartsSell')[0].show();
            Ext.ComponentQuery.query('#PartsTotal')[0].show();
            Ext.apply(Ext.ComponentQuery.query('#PartsSell')[0],     {allowBlank: false}, {}); // становится обязательным к заполнению
            Ext.apply(Ext.ComponentQuery.query('#PartsTotal')[0],    {allowBlank: false}, {}); // становится обязательным к заполнению
        } else {
            // вЫключить поля
            //Ext.getCmp('PartsSell').setVisible(false);
            //Ext.getCmp('PartsTotal').setVisible(false);
            Ext.ComponentQuery.query('#PartsSell')[0].hide();
            Ext.ComponentQuery.query('#PartsTotal')[0].hide();
            Ext.apply(Ext.ComponentQuery.query('#PartsSell')[0],     {allowBlank: true}, {}); // НЕобязательно к заполнению
            Ext.apply(Ext.ComponentQuery.query('#PartsTotal')[0],    {allowBlank: true}, {}); // НЕобязательно к заполнению
        }
    } else {

    }
}

    function ObjectsGridDblclick(ObjectTabsName, ObjectWindow, ObjectForm, ObjectAdditionsFormObj, selectedRecord ) {
        // Объединенная функция
        console.log( 'ObjectsGridDblclick(): Setting widget ObjectUploadButton SelectedObjectId to: ' + selectedRecord.data.id );
        Ext.apply(Ext.getCmp('ObjectPhotosUploadBtn'), {SelectedObjectId : selectedRecord.data.id}); // вставляем id объекта в свойство кнопки-загрузчика

        ObjectWindow.setTitle(Words_EditObjectTitle + ' №' + selectedRecord.data.id); // заголовок - редактирование
        ObjectWindow.show();            // открываем окно

        ObjectForm.getForm().reset();   // сбрасываем предыдущую форму
//Ext.getCmp('OwnerUserId').disable();
        // Открываем вкладку "Фотографии". Меняем url, обновляем превьюшки по выбранному объекту
        UpdateObjectImagesDataView( selectedRecord.data.id, ObjectTabsName, GlobVars.OpenedRealtyType);

        //                ObjectTabsName.setActiveTab(0); // переключаемся на вкладку "Характеристики"
        // id для формы с дополнительной инфой
        ObjectAdditionsFormObj.down('#LoadedObjectId').setValue( selectedRecord.data.id ); //#DBL

        // открываем вкладку "дополнительно"
        var AdditionsForm = GlobVars.NamesObj[GlobVars.OpenedRealtyType].ObjectAdditionsForm;
        Ext.getCmp(ObjectTabsName).down('#'+AdditionsForm).setDisabled(false); // сократить?

        // открываем кнопку "посмотреть на карте"
        Ext.ComponentQuery.query('#GeoWinBtn')[0].setDisabled(false);

        ObjectForm.getForm().load({     // загружаем данные в форму
            waitMsg :'Открывается объект № ' + selectedRecord.data.id,
            url     : 'Super.php',
            method  : 'GET',
            params  : {
                id  : selectedRecord.data.id,
                Action: 'OpenObject'
            },
            success: function(response, options) {
                var data = options.result.data;
                console.log('Объект открыт, ObjectType = ' + data.ObjectType );
                // загружаем тел номера владельца, выделяем

                ChangeObjectFormTrigger(data.RealtyType, data.ObjectType);

                ChangeObjectAgeTypeFormTrigger(data.ObjectAgeType); // корректируем поля для новостройки или вторички

                if(data.AltCityName !== null && data.AltCityName.length > 0) {          // заполнен альтернативный город, показываем поле
                    Ext.ComponentQuery.query('#AltCityFormItem')[0].show();
                    //AvitoCityChoosed();                                                 // сокращаем текст после выбора
                }
                ObjectAdditionsFormObj.getForm().load({                                 // Теперь подгружаем следующие формы
                    waitMsg : 'Загружается дополнительная информация',
                    url     : 'Super.php',
                    method  : 'GET',
                    params  : {
                        id  : selectedRecord.data.id,
                        Action: 'OpenObjectAdditions'
                    },
                    success: function(response, options) {
                        console.log('Дополнительная информация успешно загружена для №' + selectedRecord.data.id );
                    }
                });
                // проверяем не надо ли раскрыть доп станции метро
                FormMetro_CheckLoaded(data.Metro2StationId, data.Metro3StationId, data.Metro4StationId);
                InitUserAccessRights();

                LoadObjectOwnerPhones(ObjectForm, selectedRecord.data.OwnerUserId, data.OwnerPhoneId);

                CheckAttachClientToObjectButton();                          // обязательно ли выбирать клиента, меняем поле allowBlank

                if(parseInt(data.HasErrors) > 0) {
                    LoadObjectHistory('today', 'html');                      // загрузить сегодняшнюю ошибку в окно характеристик
                    Ext.ComponentQuery.query('#ObjectErrorPanel')[0].show(); // открыть панель
                }
            }
        });



    }

    function CitySaveAction(SaveButtons, NeedToClose) {
        TriggerSaveButtons('disable', SaveButtons );    // закрываем кнопки от двойного клика
        CheckAvitoCompatible();                         // проверка и изменение формы для авито #AvitoAltAddr
        Op_ExecAfterWork(                               // ждем завершения ajax запросов в предидущих ф-ях и сохраняем форму
            function() {
                SubmitObjectForm('ObjectWindow', 'ObjectTabs', 'ObjectForm', 'ObjectsGrid', 'ObjectAdditionsForm', NeedToClose);
                TriggerSaveButtons('enable', SaveButtons );
            }
        );

    }

    function OpenErrorFixedBtn() {
        if(typeof Ext.ComponentQuery.query('#ObjectFormErrorFixedBtn')[0] !== "undefined") {
            var btn = Ext.ComponentQuery.query('#ObjectFormErrorFixedBtn')[0];
            btn.setVisible(true);
        }
    }

    function LoadObjectOwnerPhones(f, ObjectOwnerId, OwnerPhoneId) {
        // при открыти формы загружаем список номеров владельца
        var stor = Ext.ComponentQuery.query('#OwnerPhoneId')[0];
        // обновляем стор с id владельа
        stor.getStore().setProxy({
                type        : 'ajax',
                url         : GetObjectOwnerPhonesUrl,
                reader      : { type: 'json' },
                extraParams : {
                    ObjectOwnerId   : ObjectOwnerId,
                    GetObjectOwnerPhones : true }
            }
        );
        stor.getStore().load({
            scope: this,
            callback: function(records, operation, success) {
                var ComboId = parseInt(OwnerPhoneId);
                Ext.ComponentQuery.query('#OwnerPhoneId')[0].setValue( ComboId );
            }
        });

    }

    function ChangeGridTrigger(CurrentRealtyGrid, NeededRealtyGrid) {
        console.log( 'ChangeGridTrigger: '+CurrentRealtyGrid+ ' > ' + NeededRealtyGrid );

        if(CurrentRealtyGrid != NeededRealtyGrid) {
            OpenRealtyGrid(NeededRealtyGrid);
            //return true;
        }
        return true;
    }


    function ShowGeoWin(LatitudeValue, LoadedObjectId) {
        if(LatitudeValue > 0) {
            var url = "Super.php?Action=GeoWin&ObjectId=" + LoadedObjectId;
            window.open(url, "", "width=600,height=400,toolbar=no,menubar=no,location=no,status=no");
            console.log( 'Openning url: ' + url );
        } else {
            Ext.MessageBox.show({
                title   : 'Данные о широте и долготе не определены',
                msg     : 'Сохраните объект и снова нажмите кнопку "Показать на карте"',
                buttons : Ext.MessageBox.OK,
                icon    : Ext.MessageBox.INFO
            });
        }
    }


    function TriggerSaveButtons(Action, ButtonIdsArr) {
        console.log(new Date() + ' '+arguments.callee.name + '('+Action+'...)');
        if(Action == 'disable') {
            for (var key in ButtonIdsArr) {
                console.log( 'Disabling: ' + ButtonIdsArr[key] );
                Ext.ComponentQuery.query('#' + ButtonIdsArr[key])[0].disable();
            }
        } else if(Action == 'enable') {
            for (var key in ButtonIdsArr) {
                console.log( 'Enabling: ' + ButtonIdsArr[key] );
                Ext.ComponentQuery.query('#' + ButtonIdsArr[key])[0].enable();
            }
        } else {
            // TODO протоколировать системную JS ошибку
        }
    }

    function CheckObjectFormFields(RealtyType) {
        var success = true;
        var MsgText = '';
        //console.log('CheckObjectFormFields('+RealtyType+')');
        console.log(new Date() + ' '+arguments.callee.name + '('+RealtyType+')');
        var Price  = Math.round(Ext.ComponentQuery.query('#Price')[0].value);

        if(RealtyType == 'city') {
            var SAll   = Math.round(Ext.ComponentQuery.query('#SquareAll')[0].value);
            var SLiv   = Math.round(Ext.ComponentQuery.query('#SquareLiving')[0].value);
            var SKit   = Math.round(Ext.ComponentQuery.query('#SquareKitchen')[0].value);
            var Floor  = Math.round(Ext.ComponentQuery.query('#Floor')[0].value);
            var Floors = Math.round(Ext.ComponentQuery.query('#Floors')[0].value);

            // площадь
            if(SAll < SLiv || SAll < SKit)      {success=false; MsgText = '<b>Общая площадь ('+SAll+'м)</b> не может быть меньше <b>жилой ('+SLiv+'м)</b> или <b>кухни ('+SKit+'м)</b>'; }
            else if(SLiv > SAll || SLiv < SKit) {success=false; MsgText = '<b>Жилая площадь ('+SLiv+'м)</b> не может быть больше <b>общей ('+SAll+'м)</b> или <b>меньше кухни ('+SKit+'м)</b>'; }
            else if((SLiv + SKit) > SAll)       {success=false; MsgText = 'Сумма площадей <b>жилой ('+SLiv+'м)</b> и <b>кухни ('+SKit+'м)</b> не может быть больше общей <b>('+SAll+'м)</b>'; }
            if(SAll == 0 || SLiv == 0 || SKit == 0){success=false; MsgText = 'Ни одна из площадей не должна быть равна нулю'; }
            // проверка этажности
            if(Floor > Floors)                  {success=false; MsgText = '<b>Этаж ('+Floor+')</b> не может быть больше <b>этажности дома ('+Floors+')</b>'; }
            // проверка цены
            if(Price < 10000)                   {success=false; MsgText = '<b>Цена ('+Price+')</b> не может быть меньше 10 000'; }
        } else if(RealtyType == 'country') {
            if(Price < 10000)                   {success=false; MsgText = '<b>Цена ('+Price+')</b> не может быть меньше 10 000'; }

        } else if(RealtyType == 'commerce') {
            if(Price < 10000)                   {success=false; MsgText = '<b>Цена ('+Price+')</b> не может быть меньше 10 000'; }

        } else {
            success=false;
            alert('unknown RealtyType'); // todo протоколировать системную ошибку
        }
        if(!success) {
            Ext.Msg.show({
                title   :'Ошибка',
                msg     : MsgText,
                buttons : Ext.Msg.OK,
                icon    : Ext.Msg.ERROR
            });
        }
        return success;
    }

    function FormMetro_CheckLoaded(s2,s3,s4) {
        // не раскрыть ли добавленные станции при загрузке формы
        if(s2 > 0) {
            var p = Ext.ComponentQuery.query('#Metro2Panel')[0];
            if(p) {
                p.setVisible(true);
                p.setDisabled(false);
            }
        }
        if(s3 > 0) {
            var p = Ext.ComponentQuery.query('#Metro3Panel')[0];
            if(p) {
                p.setVisible(true);
                p.setDisabled(false);
            }
        }
        if(s4 > 0) {
            var p = Ext.ComponentQuery.query('#Metro4Panel')[0];
            if(p) {
                p.setVisible(true);
                p.setDisabled(false);
            }
        }
        FormMetro_CheckMore();
    }

    function FormMetro_CheckMore() {
        // не закрыть ли кнопку "еще", если станций уже много
        var mor= Ext.ComponentQuery.query('#MetroMoreBtn')[0];
        var m2 = Ext.ComponentQuery.query('#Metro2Panel')[0];
        var m3 = Ext.ComponentQuery.query('#Metro3Panel')[0];
        var m4 = Ext.ComponentQuery.query('#Metro4Panel')[0];
        if(mor && m2 && m3 && m4) { // в загородке нет полей
            if( m2.isVisible() && m3.isVisible() && m4.isVisible() ) {
                mor.setDisabled(true);
            } else {
                mor.setDisabled(false);
            }
        }
    }

    function FormMetro_MoreMetroButtons() {
        // добавляем (раскрываем) дополнительные станции метро
        //var mor= Ext.ComponentQuery.query('#MetroMoreBtn')[0];
        var m2 = Ext.ComponentQuery.query('#Metro2Panel')[0];
        var m3 = Ext.ComponentQuery.query('#Metro3Panel')[0];
        var m4 = Ext.ComponentQuery.query('#Metro4Panel')[0];
        if(!m2.isVisible()) {
            m2.setVisible(true);
            m2.setDisabled(false);
        } else if(!m3.isVisible() ) {
            m3.setVisible(true);
            m3.setDisabled(false);
        } else if(!m4.isVisible() ) {
            m4.setVisible(true);
            m4.setDisabled(false);
        }

        FormMetro_CheckMore();
    }

    function FormMetro_CloseStation(itemId) {
        // скрываем строку со станцией и дергаем кнопку "еще"
        var m = Ext.ComponentQuery.query(itemId)[0];
        m.setVisible(false);
        m.setDisabled(true);

        FormMetro_CheckMore();
    }


    function LoadObjectHistory(Period, Format) {
        if(Period = 'today') {

            var ObjectId = Ext.ComponentQuery.query('#LoadedObjectId')[0].value;
            Ext.Ajax.request({
                url : LoadObjectHistoryUrl,
                params  : {
                    ObjectId  : ObjectId,
                    EventType : 'errors',
                    Period    : Period,
                    Format    : Format

                },
                success: function(response, opts) {
                    var obj = Ext.decode(response.responseText);
                    if(obj.success == true) {
                        var text = Ext.ComponentQuery.query('#ObjectTodayError')[0];
                        text.update(obj.data);
                    }
                },
                failure: function(response, opts) {
                    alert('ошибка при LoadObjectHistory ');
                }
            });

        } else if (Period = 'all') {

        }

    }

    function CheckAttachClientToObjectButton() {
        if(typeof GlobVars.SysParams.AttachClientToObjectStrict !== "undefined") {
            if(GlobVars.SysParams.AttachClientToObjectStrict == "1") {
                // выбор клиента в объекте обязателен
                Ext.apply(Ext.ComponentQuery.query('#OwnerClientId')[0],     {allowBlank: false}, {}); // strict
            } else {
                // клиента НЕ обязательно выбирать в объекте
                Ext.apply(Ext.ComponentQuery.query('#OwnerClientId')[0],     {allowBlank: true},  {});
            }
        }
    }