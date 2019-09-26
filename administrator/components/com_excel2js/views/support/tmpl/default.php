<?php

defined('_JEXEC') or die('Restricted access');
$view = JRequest:: getVar('view', 'excel2js', '', 'string');

?>
<style type="text/css">
    .component_version_date {
        font-weight: bold;
        color: #000099;
        text-decoration: underline;
        font-size: 14px;
    }

    .component_version {
        font-size: 14px;
    }
</style>
<?php $support_time = @strtotime($this->data->support); ?>
<div id="container">
    <div style="width: 45%;float:left">
        <h2>Тех. поддержка</h2>
        <h3>Последняя версия компонента - <?php echo @$this->data->version ?></h3>
        <h3>Установленная у Вас версия&nbsp;&nbsp;&nbsp;&nbsp; - <?php echo @$this->my_version ?>
			<?php if ( @$this->data->version != 'Установлена последняя версия' AND @version_compare($this->data->version, $this->my_version, ">") ): ?>
				<?php if ( $support_time > time() ): ?>
                    
				<?php elseif ( $support_time ): ?>
                   
				<?php endif; ?>
			<?php else: ?>
                <span style="color:green"> Установлена последняя версия</span>
			<?php endif; ?>
        </h3>
		<?php
		
		if ( $support_time ) {
			$this->data->support = date("d.m.Y", $support_time);
		}
		?>
        
        <h2>Сообщите тех. поддержке о проблемах и сложностях, которые у Вас возникли на сайте https://joom-shopping.com</h2>
        
    </div>
    <div style="width: 50%;float:left; padding-left: 20px;">
        <h2>Список изменений</h2>
		<div class="moduletable">
	<span class="component_version_date">22.04.2018</span>&nbsp;<span class="component_version">(v 3.1.0)</span> ВКонтакте - из выборки товаров для экспорт исключены товары, которых нет на складе. Добавлены следующие настройки:<br>
1) Экспорт фотографий: Главное фото / Все фото <br>
2) Фильтр характеристик, которые нужно включить в описание <br>
3) Экспорт независимых атрибутов в описании: Да / Нет <br>
4) Фильтр независимых атрибутов, которые нужно включить в описание <br> 
5) Вставить ссылку на товар в описании: Да / Нет <br>
При превышении лимита количества запросов к API теперь выводится CAPTCHA.  </p><p><span class="component_version_date">16.04.2018</span>&nbsp;<span class="component_version">(v 3.0.0)</span> Добавлен новый раздел - "ВКонтакте". На этой странице Вы сможете экспортировать товары из JoomShopping в группу ВКонтакте (VK Market). Также можно просматривать и удалять товары группы ВК прямо из админки Joomla. Товары для экспорта можно фильтровать по категориям и/или производителям.  </p><p><span class="component_version_date">23.12.2017</span>&nbsp;<span class="component_version">(v 2.7.0)</span> YML-импорт: <br>1) добавлена поддержка импорта зависимого атрибута. Перед импортом необходимо создать через админку JS зависимый атрибут с таким же названием как в YML-файле в тэге "param". Идентификация товаров осуществляется по атрибуту "group_id" в тэге "offer".<br>
2) добавлена поддержка импорта количества товара через тэг "outlets"</p><p><span class="component_version_date">5.12.2017</span>&nbsp;<span class="component_version">(v 2.6.0)</span> в алгоритм импорта добавлена функция автоматической конвертации скачиваемых по ссылке bmp-изображений в формат JPEG</p><p><span class="component_version_date">6.10.2017</span>&nbsp;<span class="component_version">(v 2.5.0)</span> <br> - в настройках профиля для Excel добавлена опция "Обновление цен". Вы можете выбрать условие, при котором цены у существующих товаров будут обновляться. Условия: 1) Цена в прайсе выше, чем цена на сайте; 2) Цена в прайсе ниже, чем цена на сайте.
<br> - если Вы меняли в файлах JS путь к папке изображений, то компонент во время импорта будет использовать этот измененный путь.
<br> - Excel-импорт по расписанию: добавлена возможность указать в ссылке для Cron название профиля и ссылку на удаленный файл. Таким образом Вы можете создать несколько заданий, которые будут работать по разным профилям и скачивать разные файлы. Но не делайте так, чтобы эти задания срабатывали в одно время!</p><p><span class="component_version_date">26.09.2017</span>&nbsp;<span class="component_version">(v 2.4.0)</span> <br> - в настройках профиля для Excel добавлена опция "Обновлять SEO-параметры?". При оключении данной опции такие поля как Title страницы, Полное описание, Мета-описание, Ключевые слова не будут обновляться у существующих до момента импорта товаров. Эти данные будут заполняться только у товаров, которые создаются в процессе импорта.
<br> - Загрузка изображений с внешнего сервера - в опцию "Для новых товаров" добавлены товары без изображений. Т.о. если выбрать опцию "Для новых и товаров без изображения", то изображения будут загружаться для новых товаров, а также существующих товаров без изображений.</p><p><span class="component_version_date">2.08.2017</span>&nbsp;<span class="component_version">(v 2.3.0)</span> в настройках профиля добавлен столбец "Артикул (Зависимый атр.)". Столбец появится, если версия JoomShopping - 4.16.1 или выше</p><p><span class="component_version_date">10.06.2017</span>&nbsp;<span class="component_version">(v 2.2.0)</span> в общих настройках добавлена опция "Переименование скачиваемых изображений". Включение функции приведет к тому, что изображения,скачиваемые по прямой ссылке в прайсе, будут переименованы. За основу нового имени файла изображения берется код товара или название (товара или категории).</p><p><span class="component_version_date">27.05.2017</span>&nbsp;<span class="component_version">(v 2.1.0)</span> в настройках профиля добавлен столбец "Артикул", который появился в JoomShopping 4.16.1</p><p><span class="component_version_date">5.02.2017</span>&nbsp;<span class="component_version">(v 2.0.0)</span> в компонент добавлен функционал для поддержки YML-файлов (Яндекс.Маркет). Теперь Вы можете импортировать товары из YML (основные поля и характеристики) и создавать YML-файлы с Вашими товарами для загрузки на Яндекс.Маркет (основные поля и характеристики).</p><p><span class="component_version_date">4.07.2016</span>&nbsp;<span class="component_version">(v 1.2.9)</span> улучшен алгоритм расчета минимальной цены. Теперь алгоритмом учитываются зависимые и независимые атрибуты, а также скидки от количества товара. </p><p><span class="component_version_date">30.05.2016</span>&nbsp;<span class="component_version">(v 1.2.8)</span> в профиль настроек добавлена возможность указывать статус публикации для новых и обновляемых товаров отдельно.</p><p><span class="component_version_date">24.03.2016</span>&nbsp;<span class="component_version">(v 1.2.7)</span> исправлен баг с чекбоксом "Неограничено", который возникал при импорте прайса без указания количества товара.</p><p><span class="component_version_date">14.03.2016</span>&nbsp;<span class="component_version">(v 1.2.6)</span> улучшена библиотека для изменения размеров загружаемых изображений</p><p><span class="component_version_date">2.03.2016</span>&nbsp;<span class="component_version">(v 1.2.5)</span> снижено потребление оперативной памяти для процесса восстановления бэкапа из SQL-файла. Теперь SQL-файлы бэкапа объемом более 50 МБ не будут вызывать ошибок, связанных с нехваткой оперативной памяти, во время восстановления.</p><p><span class="component_version_date">6.02.2016</span>&nbsp;<span class="component_version">(v 1.2.4)</span> В настройках профиля добавлено поле "Количество для зависимых атрибутов". Если при импорте зависимого атрибута, в прайсе не указано его количество, то оно будет взято из этого поля</p><p><span class="component_version_date">5.02.2016</span>&nbsp;<span class="component_version">(v 1.2.3)</span> Исправлен баг, из-за которого при определенных условиях экспорта мог не работать фильтр по производителю</p><p><span class="component_version_date">8.12.2015</span>&nbsp;<span class="component_version">(v 1.2.0)</span>  - Добавлено поле "Список характеристик". В этом поле вы можете указать все необходимые характеристики, совмещенные в одной ячейке. Отделяются характеристики друг от друга вертикальной чертой (|). Название характеристики отделяется от значений двоеточием (:). Значения характеристик (если их несколько) отделяются запятыми (,). Пример:<br><b>Цвет:Красный,Синий,Желтый|Размер:XXL,XL|Материал:"Замшевый"</b><br>Если характеристика с указанным названием не существует, то она будет создана. Тип характеристики по умолчанию - список. Если значение характеристики заключено в кавычки, то тип характеристики будет - текст.<br><br> - Добавлена возможность загружать изображения с удаленного сервера только для новых товаров, что позволит ускорить процесс импорта, если в прайсе много товаров, которые уже присутствуют на Вашем сайте и не нуждаются в обновлении изображения.</p><p><span class="component_version_date">30.11.2015</span>&nbsp;<span class="component_version">(v 1.1.2)</span> для способа маркировки категорий "Название категории для каждого товара" добавлено 2 настройки: "Разделитель уровня вложенности" (Например - Категория\Подкатегория. Вместо "\" можно указать свой символ) и "Разделитель категорий" (Например - Категория1|Категория2. Вместо "|" можно указать свой символ)</p><p><span class="component_version_date">26.11.2015</span>&nbsp;<span class="component_version">(v 1.1.1)</span> в настройках компонента на вкладке "Импорт по расписанию" добавлено поле "Удаленный файл". Если в этом поле указать файл, который находится на другом сайте (на сайте поставщика), то этот файл будет автоматически скачиваться, а затем - импортироваться.</p><p><span class="component_version_date">27.10.2015</span>&nbsp;<span class="component_version">(v 1.1.0)</span> в профиль настроек добавлено поле "Коэффициент перерасчета". На значение, указанное в этом поле будут умножаться все основные цены во время импорта. Во время экспорта наоборот - все цены будут делиться на этот коэффициент.</p><p><span class="component_version_date">28.08.2015</span>&nbsp;<span class="component_version">(v 1.0.9)</span> добавлено поле 'Изображение (Зависимый атр.)', которое позволяет назначить одно или несколько изображений набору зависимых атрибутов. Если изображений несколько их нужно указать в одной ячейке через запятую. Можно также указывать абсолютный путь (с http://), если изображение необходимо скачать с внешнего сайта.</p><p><span class="component_version_date">17.08.2015</span>&nbsp;<span class="component_version">(v 1.0.8)</span> Добавлена возможность экспорта товаров по одному или нескольким производителям</p><p><span class="component_version_date">12.08.2015</span>&nbsp;<span class="component_version">(v 1.0.7)</span> <br> - Исправлен баг при импорте сопутствующих товаров.<br> - Исправлен баг при экспорте зависимых атрибутов.<br> - Исправлен баг при расчете минимальной цены товара.<br> - Исправлен баг, из-за которого количество товара оставалось Неограниченным после того, как было указано количество на складе при последующем импорте. </p><p><span class="component_version_date">26.03.2015</span>&nbsp;<span class="component_version">(v 1.0.6)</span> Добавлено поле 'Сортировка товаров', которое позволяет задать нужную сортировку вручную.</p><p><span class="component_version_date">14.03.2015</span>&nbsp;<span class="component_version">(v 1.0.5)</span> Добавлена возможность очистки всех опций характеристик и атрибутов товаров. Добавлена возможность удаления всех резервных копий.</p><p><span class="component_version_date">09.02.2015</span>&nbsp;<span class="component_version">(v 1.0.1)</span> Добавлена возможность экспорта и импорта профиля настроек, в т.ч. полей Характеристик, Атрибутов и Дополнительных цен</p>		</div>

    </div>
</div>



