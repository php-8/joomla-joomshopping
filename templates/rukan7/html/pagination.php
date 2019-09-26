<?php
/**
* @package Helix Framework
* @author JoomShaper http://www.joomshaper.com
* @copyright Copyright (c) 2010 - 2015 JoomShaper
* @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
*/
//no direct accees
defined ('_JEXEC') or die ('resticted aceess');

function pagination_list_render($list)
{
	$currentPage = 1;
	$range = 1;
	$step = 5;
	foreach ($list['pages'] as $k => $page)
	{
		if (!$page['active'])
		{
			$currentPage = $k;
		}
	}
	if ($currentPage >= $step)
	{
		if ($currentPage % $step == 0)
		{
			$range = ceil($currentPage / $step) + 1;
		}
		else
		{
			$range = ceil($currentPage / $step);
		}
	}
	$html = '<div class="pagination-wraper">';
	$html .= '<ul class="pagination">';
	$html .= $list['previous']['data'];
	foreach ($list['pages'] as $k => $page)
	{
		if (in_array($k, range($range * $step - ($step + 1), $range * $step)))
		{
			if (($k % $step == 0 || $k == $range * $step - ($step + 1)) && $k != $currentPage && $k != $range * $step - $step)
			{
				$page['data'] = preg_replace('#(<a.*?>).*?(</a>)#', '$1...$2', $page['data']);
			}
		}

		$html .= $page['data'];
	}
	$html .= $list['next']['data'];
	$html .= '</ul>';
	$html .= '</div>';
	return $html;
}
function pagination_item_active(&$item)
{
	$class = '';
	if ($item->text == JText::_('JPREV'))
	{
		$display = '<i class="icon-arrow-left"></i>';
	}
	if ($item->text == JText::_('JNEXT'))
	{
		$display = '<i class="icon-arrow-right"></i>';
	}
	if (!isset($display))
	{
		$display = $item->text;
		$class   = ' class="hidden-xs"';
	}
	return '<li' . $class . '><a title="' . $item->text . '" href="' . $item->link . '" class="pagenav">' . $display . '</a></li>';
}
function pagination_item_inactive(&$item)
{
	if ($item->text == JText::_('JPREV'))
	{
		return '<li class="disabled"><a class=""><i class="icon-arrow-left"></i></a></li>';
	}
	if ($item->text == JText::_('JNEXT'))
	{
		return '<li class="disabled"><a class=""><i class="icon-arrow-right"></i></a></li>';
	}
	if (isset($item->active) && ($item->active))
	{
		return '<li class="active hidden-xs"><a>' . $item->text . '</a></li>';
	}
	return '<li class="disabled hidden-xs"><a>' . $item->text . '</a></li>';
}