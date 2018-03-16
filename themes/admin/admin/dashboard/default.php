<div class="card">
    <div class="card-header-big">
        <h1>Панель управления</h1>
        <p>Основные элементы панели управления:</p>
    </div>
    <div class="card-section">
        <div class="input-group">
            <span style="flex-basis:2.5em;text-align:center;">ru</span>
            <input type="text" class="form-control">
            <button class="btn btn-primary btn-square"><i class="fa fa-language"></i></button>
        </div>
        <div class="input-table mtb-1">
            <div class="input-group">
                <span style="flex-grow:0;flex-basis:5em;">ru</span>
                <input type="text" class="form-control">
                <button class="btn btn-danger btn-square"><i class="fa fa-times"></i></button>
            </div>
            <div class="input-group">
                <span style="flex-grow:0;flex-basis:5em;">en</span>
                <input type="text" class="form-control">
                <button class="btn btn-danger btn-square"><i class="fa fa-times"></i></button>
            </div>
            <div class="input-group">
                <span style="flex-grow:0;flex-basis:5em;">fi</span>
                <input type="text" class="form-control">
                <button class="btn btn-danger btn-square"><i class="fa fa-times"></i></button>
            </div>
            <div class="input-group">
                <select class="form-control" style="flex-grow:0;flex-basis:5em;">
                    <option>ru Russian русский</option>
                    <option>en English</option>
                    <option>ar Arabic</option>
                    <option>fi Finnish suomi</option>
                    <option>fi Finnish suomi</option>
                    <option>fi Finnish suomi</option>
                    <option>fi Finnish suomi</option>
                    <option>fi Finnish suomi</option>
                    <option>fi Finnish suomi</option>
                    <option>fi Finnish suomi</option>
                    <option>fi Finnish suomi</option>
                    <option>fi Finnish suomi</option>
                    <option>fi Finnish suomi</option>
                    <option>fi Finnish suomi</option>
                    <option>fi Finnish suomi</option>
                    <option>fi Finnish suomi</option>
                    <option>fi Finnish suomi</option>
                    <option>fi Finnish suomi</option>
                    <option>fi Finnish suomi</option>
                    <option>fi Finnish suomi</option>
                    <option>fi Finnish suomi</option>
                    <option>fi Finnish suomi</option>
                    <option>fi Finnish suomi</option>
                    <option>fi Finnish suomi</option>
                    <option>fi Finnish suomi</option>
                    <option>fi Finnish suomi</option>
                    <option>fi Finnish suomi</option>
                    <option>fi Finnish suomi</option>
                    <option>fi Finnish suomi</option>
                    <option>fi Finnish suomi</option>
                </select>
                <input type="text" class="form-control">
                <button class="btn btn-success btn-square"><i class="fa fa-plus"></i></button>
            </div>
        </div>
    </div>
    <div class="card-section">
        <p>
            <button type="button" class="btn btn-primary">Кнопка</button>
            <button type="button" class="btn btn-success">Кнопка</button>
            <button type="button" class="btn btn-danger">Кнопка</button>
            <button type="button" class="btn btn-default">Кнопка</button>
            <button type="button" class="btn btn-primary" disabled>Кнопка</button>
            <button type="button" class="btn btn-success" disabled>Кнопка</button>
            <button type="button" class="btn btn-danger" disabled>Кнопка</button>
            <button type="button" class="btn btn-default" disabled>Кнопка</button>
        </p>
        <p>
            <textarea class="form-control">Textarea</textarea>
        </p>
        <p>
            <textarea class="form-control" disabled>Disabled textarea</textarea>
        </p>
        <p>
            <input class="form-control" type="text" value="Textfield"><br>
            <input class="form-control" type="text" value="Textfield disabled" disabled><br>
            <select class="form-control">
                <option>Select</option>
            </select><br>
            <select class="form-control" disabled>
                <option>Select</option>
            </select><br>
        </p>
        <p>
            <label><input hidden type="radio" name="r1" checked><span></span> Radio</label>
            <label><input hidden type="radio" name="r1"><span></span> Radio</label>
            <label><input hidden type="radio" name="r2" checked disabled><span></span> Radio</label>
            <label><input hidden type="radio" name="r2" disabled><span></span> Radio</label>
            <label><input hidden type="checkbox" name="c1" checked><span></span> Checkbox</label>
            <label><input hidden type="checkbox" name="c2"><span></span> Checkbox</label>
            <label><input hidden type="checkbox" name="c3" checked disabled><span></span> Checkbox</label>
            <label><input hidden type="checkbox" name="c4" disabled><span></span> Checkbox</label>
        </p>
        <p>
            <fieldset>
                <legend><label><input hidden type="checkbox" name="c2"><span></span> Checkbox</label></legend>
                <input class="form-control" type="text" value="Textfield"><br>
                <input class="form-control" type="text" value="Textfield disabled" disabled><br>
                <p>
                    <label><input hidden type="radio" name="r11" checked><span></span> Radio</label>
                    <label><input hidden type="radio" name="r11"><span></span> Radio</label>
                    <label><input hidden type="checkbox" name="c1" checked><span></span> Checkbox</label>
                    <label><input hidden type="checkbox" name="c2"><span></span> Checkbox</label>
                    <textarea class="form-control">Textarea</textarea>
                </p>
                <label><input hidden type="radio" name="r11" checked><span></span> Radio</label>
                <label><input hidden type="radio" name="r11"><span></span> Radio</label>
                <label><input hidden type="checkbox" name="c1" checked><span></span> Checkbox</label>
                <label><input hidden type="checkbox" name="c2"><span></span> Checkbox</label>
                <p>
                    <button type="button" class="btn btn-primary">Кнопка</button>
                    <button type="button" class="btn btn-success">Кнопка</button>
                    <button type="button" class="btn btn-danger">Кнопка</button>
                    <button type="button" class="btn btn-default">Кнопка</button>
                    <button type="button" class="btn btn-primary" disabled>Кнопка</button>
                    <button type="button" class="btn btn-success" disabled>Кнопка</button>
                    <button type="button" class="btn btn-danger" disabled>Кнопка</button>
                    <button type="button" class="btn btn-default" disabled>Кнопка</button>
                </p>
            </fieldset>
        </p>
        <p>
            <fieldset disabled>
                <legend><label><input hidden type="checkbox" name="c2"><span></span> Checkbox</label></legend>
                <input class="form-control" type="text" value="Textfield"><br>
                <input class="form-control" type="text" value="Textfield disabled" disabled><br>
                <p>
                    <label><input hidden type="radio" name="r11" checked><span></span> Radio</label>
                    <label><input hidden type="radio" name="r11"><span></span> Radio</label>
                    <label><input hidden type="checkbox" name="c1" checked><span></span> Checkbox</label>
                    <label><input hidden type="checkbox" name="c2"><span></span> Checkbox</label>
                    <textarea class="form-control">Textarea</textarea>
                </p>
                <label><input hidden type="radio" name="r11" checked><span></span> Radio</label>
                <label><input hidden type="radio" name="r11"><span></span> Radio</label>
                <label><input hidden type="checkbox" name="c1" checked><span></span> Checkbox</label>
                <label><input hidden type="checkbox" name="c2"><span></span> Checkbox</label>
                <p>
                    <button type="button" class="btn btn-primary">Кнопка</button>
                    <button type="button" class="btn btn-success">Кнопка</button>
                    <button type="button" class="btn btn-danger">Кнопка</button>
                    <button type="button" class="btn btn-default">Кнопка</button>
                    <button type="button" class="btn btn-primary" disabled>Кнопка</button>
                    <button type="button" class="btn btn-success" disabled>Кнопка</button>
                    <button type="button" class="btn btn-danger" disabled>Кнопка</button>
                    <button type="button" class="btn btn-default" disabled>Кнопка</button>
                </p>
            </fieldset>
        </p>
        <div class="alert alert-success">
            Операция выполнена успешно
        </div>
        <div class="alert alert-info">
            Операция выполнена
        </div>
        <div class="alert alert-danger">
            Операция выполнена с ошибками
        </div>
    </div>
</div>
