<div class="card-section">
    <button class="btn btn-success"><i class="fa fa-plus"></i><span class="hidden-sm-down"> Добавить</span></button>
    <button class="btn btn-danger" data-action="deleteSelected"><i class="fa fa-trash"></i><span class="hidden-sm-down"> Удалить</span></button>

    <button class="btn btn-default" data-action="copySelected"><i class="fa fa-copy"></i><span class="hidden-sm-down"> Копировать</span></button>
    <button class="btn btn-default" data-action="cutSelected"><i class="fa fa-cut"></i><span class="hidden-sm-down"> Вырезать</span></button>
    <button class="btn btn-default" data-action="pasteCopied"><i class="fa fa-level-down"></i><span class="hidden-sm-down"> Вставить</span></button>

    <button class="btn btn-default"><i class="fa fa-filter"></i><span class="hidden-sm-down"> Фильтр</span></button>
    <button class="btn btn-default"><i class="fa fa-sliders"></i><span class="hidden-sm-down"> Вид</span></button>
</div>

<div class="card-section">
    <div class="table-wrap">
        <table class="table">
            <tr>
                <th></th>
                <th>
                    <label><input type="checkbox" hidden data-action="selectAll"><span></span></label>
                </th>
                <th>ID</th>
                <th>Строка</th>
                <th>Число</th>
                <th>Дробь</th>
                <th>Код</th>
                <th>Список файлов</th>
                <th>Дата и время</th>
                <th>Цвет</th>
                <th>Опции</th>
                <th>Хеш</th>
                <th>Ссылка</th>
            </tr>
            <?php for($i=0; $i<25; $i++) { ?>
            <tr>
                <td>
                    <button class="btn btn-square btn-default" data-wf-actions='{"click":[{"action":"showDropdown"}]}'>
                        <ul class="dropdown hidden">
                            <li><i class="fa fa-folder-open-o"></i>Открыть вложенные</li>
                            <li class="separator"></li>
                            <li><i class="fa fa-cut"></i>Вырезать</li>
                            <li><i class="fa fa-copy"></i>Копировать</li>
                            <li><i class="fa fa-pencil-square-o"></i>Изменить</li>
                            <li class="separator"></li>
                            <li><i class="fa fa-times" style="color:#D32F2F"></i>Удалить</li>
                        </ul>
                        <i class="fa fa-ellipsis-h"></i>
                    </button>
                </td>
                <td>
                    <label><input type="checkbox" hidden data-action="selectAll"><span></span></label>
                </td>
                <td>12345</td>
                <td>Строка текста без тегов</td>
                <td>12345</td>
                <td>12345.67</td>
                <td>
                    <div class="chip chip-default"><div class="chip-icon"><i class="fa fa-code"></i></div>{"json":"code", ...</div>
                </td>
                <td>
                    <div class="chip chip-default"><div class="chip-icon"><i class="fa fa-paperclip"></i></div><a href="/4545">12345678-1123.txt</a></div>
                    <div class="chip chip-primary"><div class="chip-icon"><i class="fa fa-paperclip"></i></div>12345678-1123.jpg</div>
                    <div class="chip chip-success"><div class="chip-icon"><i class="fa fa-paperclip"></i></div>12345678-1123.txt</div>
                    <div class="chip chip-danger"><div class="chip-icon"><i class="fa fa-paperclip"></i></div>12345678-1123.txt</div>
                </td>
                <td>13.01.2015 13:48:54</td>
                <td>
                    <div class="chip chip-default"><div class="chip-icon" style="background-color:#C0FFEE"></div>#C0FFEE</div>
                </td>
                <td>
                    <div class="chip chip-primary">чай</div>
                    <div class="chip chip-primary">кофе</div>
                    <div class="chip chip-primary">какао</div>
                    <div class="chip chip-primary">молоко</div>
                </td>
                <td>
                    <div class="chip chip-danger"><div class="chip-icon"><i class="fa fa-key"></i></div>md5</div>
                </td>
                <td>
                    <div class="chip chip-primary"><div class="chip-icon"><i class="fa fa-link"></i></div>articles-56</div>
                </td>
            </tr>
            <?php } ?>
        </table>
    </div>
</div>

<div class="card-section c">
    <p>Всего записей в таблице "Имя таблицы": 589</p>
    <?=block('pagination', array('url' => '%u', 'thisPage' => 25, 'numPages' => 50))?>
</div>

<!-- <div class="system-message alert alert-danger" data-wf-actions='{"mouseover":[{"action":"toggleModal"}]}'>
    Системное сообщение
</div> -->