/*
 Модуль Operations.js

 Назначение:
 Сделать ход алгоритма последовательным. Задерживать все функции в ожидании завершения предидущих.
 Синглтон.

 Переменные:
 Op.BlockCount     // Счетчик текущих блокирующих операций, при которых не может произойти новая операция
 // Для начала новой операции, нужно чтобы BlockOperations был = 0, для этого вводится ожидание-sleep
 Op.SuccessOpCount // Счетчик, сколько всего было успешных операций (Зачем?)
 Op.FailureOpCount // Счетчик, сколько было ошибок (Для сохранения ошибок в сессии пользователя)

 Функции:
 Op_Start()     // Увеличивает счетчик текущих блокирующих операций
 Op_Stop()      // Уменьшает счетчик
 Op_ErrorStop() // Уменьшает счетчик с протоколированием ошибки
 Op_IsFree()    // Проверить, нет ли зависших операций (равен ли счетчик 0)

 Пример: выполнить ajax запрос только после проверки формы:

    function FormCHecker() {
        Op_Start(this);                     // Включаем блок дальнейших операций до вызова Op_Stop()
        ... много действий по проверке формы
        Op_Stop();                          // разблокировка
        Op_ErrorStop('ошибка там-то....');  // или разблокировка с ошибкой
    }

    FormCHecker():                          // начинаем проверять форму

    Op_ExecAfterWork(  // ждем завершения FormCHecker()
        function() {
        ... выполняем ajax запрос
        }
    );


 */

GlobVars.Op = {
    // управляемые
    CheckTime       : 500, // пауза между проверками (милисек)
    MaxAttemps      : 20,  // макс кол-во попыток до // TODO остановки?
    // внутренние
    BlockCount      : 0, // Счетчик текущих активных блокирующих операций, при которых не может произойти новая операция
                         // Для начала новой операции, нужно чтобы BlockOperations был = 0, для этого вводится ожидание-sleep
    SuccessOpCount  : 0, // Счетчик, сколько всего было успешных операций (Зачем?)
    FailureOpCount  : 0, // Счетчик, сколько было ошибок (Для сохранения ошибок в сессии пользователя)
    AttempsCount     : 0  // Счетчик попыток
};

function Op_Start() {
    console.log(new Date() + ' ' + arguments.callee.name+ '()');
    GlobVars.Op.BlockCount++;
}
function Op_Stop() {
    console.log(new Date() + ' ' + arguments.callee.name+ '()');
    GlobVars.Op.BlockCount--;
}
function Op_ErrorStop() {
    console.log(new Date() + ' ' + arguments.callee.name+ '()');
    GlobVars.Op.BlockCount--;
    //TODO протоколирование
}
function Op_ExecAfterWork(Func) {
    var d = new Date();
    console.log(GlobVars.Op.AttempsCount + ') '+ d + ' ' + arguments.callee.name+ '()');
    if(GlobVars.Op.BlockCount == 0) {   // нет запущенных происходящих операций, выполняем задачу;
        Func();
    } else {                            // что-то уже происходит, задерживаем задачу;
        GlobVars.Op.AttempsCount++;     // TODO по достижении максимума что делаем?
        if(GlobVars.Op.AttempsCount > GlobVars.Op.MaxAttemps) {
            alert('Возникла проблема доступа к сети или другая ошибка.\n'+Words_CallProgrammerMsg);
            return;
        }
        setTimeout(function() {
            Op_ExecAfterWork(Func);
        }, GlobVars.Op.CheckTime);
    }
    return true;
}
