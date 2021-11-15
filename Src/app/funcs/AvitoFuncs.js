
function CheckAvitoCompatible() { //#AvitoAltAddr
    // Задача 1: если наспункта нет в справочнике авито, показать доп поле с соответствующими региону наспунктами.
    var ok        = false;
    var CityName   = Ext.ComponentQuery.query('#KladrCity')[0].getValue();
    var RegionName = Ext.ComponentQuery.query('#KladrRegion')[0].getValue();
    var RegOblast  = Ext.ComponentQuery.query('#CityType_Oblast')[0].getValue(); // bool
    var RegMoscow  = Ext.ComponentQuery.query('#CityType_Moscow')[0].getValue(); // bool
    if(RegMoscow) {
        if(CityName == 'Москва') {              // Точно москва -> ничего не меняем;
            TriggerAvitoCityField('close');
        } else {                                // если наспункт != москва (делаем доп запрос в авито справчнк, если наспункт есть - ничего не далаем,
            AvitoFuncs_CheckCityInRegion(RegionName, CityName);     // иначе: показываем доп селект с городами из мск обл.
        }
    } else if(RegOblast) {
        if(CityName == 'Москва' && RegionName == 'Московская область') { // Точно москва -> ничего не меняем;
            TriggerAvitoCityField('close');
        } else {                                                        // Показывать города в выбранной области
            AvitoFuncs_CheckCityInRegion(RegionName, CityName);
        }
    } else {
        console.log(RegMoscow);
        console.log(RegOblast);
    }
}
function AvitoFuncs_CheckCityInRegion(RegionName, CityName) {
    Op_Start(this);// Включаем блок дальнейших операций до вызова Op_Stop()
    Ext.Ajax.request({
        url : CheckCityInRegionUrl,
        params  : {
            RegionName  : RegionName,
            CityName    : CityName
        },
        success: function(response, opts) {
            var obj = Ext.decode(response.responseText);
            if(obj.CityExist == true) {
                TriggerAvitoCityField('close');      //
            } else {
                TriggerAvitoCityField('open');
            }
            Op_Stop(); // разблокировка //TODO подумать (возможно лучше ставить внутри ф-ий?)
        },
        failure: function(response, opts) {
            //todo ??
            Op_ErrorStop('ошибка там-то....');
        }
    });
}

function TriggerAvitoCityField(Action) {// open/close
    var CityValue   = Ext.ComponentQuery.query('#KladrCity')[0].getValue();
    var FormItem    = Ext.ComponentQuery.query('#AltCityFormItem')[0];
    var AltCityName = Ext.ComponentQuery.query('#AltCityName')[0];
    if(Action == 'open') {
        if(GlobVars.RegionOrCityUpdated > 0) { // если регион с городом изменили
            console.log('Открываем поле альтернативного наспункта');
            //console.log(AltCityName);
            FormItem.show();                                                            // показываем поле
            Ext.apply(AltCityName, { allowBlank: false }, {});                          // поле становится обязательным

            if(typeof AltCityName == Object && AltCityName.getValue().length <= 0) {
                console.log('сброс поля AltCityName');
                AltCityName.reset();
            }
            if(FormItem.hidden == true) {
                Ext.Msg.show({                                                          // ошибка "Населенный пункт не обнаружен"
                    title   :'Необходимо уточнение',
                    msg     : Words_AvitoCityNotFound + Words_AvitoCityChoose,
                    buttons : Ext.Msg.OK,
                    icon    : Ext.Msg.WARNING
                });
                console.log('сброс поля AltCityName');
                AltCityName.reset();
            }
            //AltCityName.setFieldLabel(Words_AvitoCityNotFound + Words_AvitoCityChoose); // показываем текст после выбора

        } else {
            console.log('RegionOrCityUpdated: '+GlobVars.RegionOrCityUpdated+', поле альт. наспункта НЕ обновляем');
        }
    } else if(Action == 'close') {
        console.log('Закрываем поля альтернативного наспункта');
        Ext.apply(AltCityName, { allowBlank: true }, {});                          // поле становится НЕ обязательным
        FormItem.hide();                                                            // скрываем поле
        AltCityName.reset();
    } else {
        alert('param absent');
    }

}

function AvitoCityChoosed() {
    Ext.ComponentQuery.query('#AltCityName')[0].setFieldLabel(Words_AvitoCityChoosed); // сокращаем текст после выбора
}

function UpdateAltCityStore() {             // обновить поле с новыми параметрами (если нужно)
    if(GlobVars.RegionOrCityUpdated == 1) { // если регион с городом изменили
        GlobVars.RegionOrCityUpdated = 0;   // снимаем маркер из-зи ненадобности обновлять по 100 раз
        // меняем параметр для выборки города с условием региона из справочника Авито
        var s = Ext.data.StoreManager.lookup('ObjectForm.AvitoAltCityStore');
        s.proxy.extraParams = { ChosenRegion : Ext.ComponentQuery.query('#KladrRegion')[0].getValue(),
                                ChosenCity   : Ext.ComponentQuery.query('#KladrCity')[0].getValue() };
        s.reload();
    } else {
        console.log('RegionOrCityUpdated не менялось');
    }
}