<?php

/**
 * wbz
 */

namespace Admin\Logic;

use Think\Model\RelationModel;
/**
 * 分类逻辑定义
 * Class CatsLogic
 * @package Admin\Logic
 */
class ArticleCatLogic extends RelationModel
{

    /**
     * 获得指定分类下的子分类的数组
     *
     * @access  public
     * @param   int     $cat_id     分类的ID
     * @param   int     $selected   当前选中分类的ID
     * @param   boolean $re_type    返回的类型: 值为真时返回下拉列表,否则返回数组
     * @param   int     $level      限定返回的级数。为0时返回所有级数
     * @return  mix
     */
    public function article_cat_list($cat_id = 0, $selected = 0, $re_type = true, $level = 0)
    {
        static $res = NULL;


        if ($res === NULL)
        {
            $data = false;//read_static_cache('art_cat_pid_releate');
            if ($data === false)
            {
                $sql = "SELECT c.*, COUNT(s.id) AS has_children FROM __PREFIX__article_cat AS c".
                    " LEFT JOIN __PREFIX__article_cat AS s ON s.parent_id=c.id".
                    " GROUP BY c.id  ORDER BY parent_id, sort_order";
                $res = D('article_cat')->query($sql);
                //write_static_cache('art_cat_pid_releate', $res);
            }
            else
            {
                $res = $data;
            }
        }
    
        if (empty($res) == true)
        {
            return $re_type ? '' : array();
        }


        $options = $this->article_cat_options($cat_id, $res); // 获得指定分类下的子分类的数组

        return $options;


    }

    /**
     * @author wbz
     * @param int $cat_id
     * @param int $selected
     * @param bool $re_type
     * @param int $level
     * @return mixed
     */
    public function art_cat_list($cat_id = 0, $selected = 0, $re_type = true, $level = 0)
    {
        global $art_category, $art_category2;
        $sql = "SELECT * FROM  __PREFIX__article_cat ORDER BY parent_id , sort_order ASC";
        $art_category = D('article_cat')->query($sql);
        $art_category = convert_arr_key($art_category, 'id');


        foreach ($art_category AS $key => $value)
        {
            if($value['parent_id'] == 0)
                $this->get_cat_tree($value['id']);
        }

        if($re_type == true){//返回有缩进的
            foreach($art_category2 as &$val){
                $str = '';
                for($i=0; $i<($val['level']-1); $i++){
                    $str .= "&nbsp;&nbsp;&nbsp;&nbsp;";
                }

                $val['cat_name'] = $str.$val['cat_name'];
            }
        }
        return $art_category2;
    }

    /**
     * 获取指定id下的 所有分类
     * @global type $goods_category 所有商品分类
     * @param type $id 当前显示的 菜单id
     * @return 返回数组 Description
     */
    public function get_cat_tree($id)
    {
        global $art_category, $art_category2;
        $art_category2[$id] = $art_category[$id];
        foreach ($art_category AS $key => $value){
            if($value['parent_id'] == $id)
            {
                $this->get_cat_tree($value['id']);
                $art_category2[$id]['have_son'] = 1; // 还有下级
            }
        }
    }



    
    /**
     * 过滤和排序所有文章分类，返回一个带有缩进级别的数组
     *
     * @access  private
     * @param   int     $cat_id     上级分类ID
     * @param   array   $arr        含有所有分类的数组
     * @param   int     $level      级别
     * @return  void
     */
    public function article_cat_options($spec_cat_id, $arr)
    {
        static $cat_options = array();
    
        if (isset($cat_options[$spec_cat_id]))
        {
            return $cat_options[$spec_cat_id];
        }
    
        if (!isset($cat_options[0]))
        {
            $level = $last_cat_id = 0;
            $options = $cat_id_array = $level_array = array();
            while (!empty($arr))
            {
                foreach ($arr AS $key => $value)
                {
                    $cat_id = $value['cat_id'];
                    if ($level == 0 && $last_cat_id == 0)
                    {
                        if ($value['parent_id'] > 0)
                        {
                            break;
                        }
    
                        $options[$cat_id]          = $value;
                        $options[$cat_id]['level'] = $level;
                        $options[$cat_id]['id']    = $cat_id;
                        $options[$cat_id]['name']  = $value['cat_name'];
                        unset($arr[$key]);
    
                        if ($value['has_children'] == 0)
                        {
                            continue;
                        }
                        $last_cat_id  = $cat_id;
                        $cat_id_array = array($cat_id);
                        $level_array[$last_cat_id] = ++$level;
                        continue;
                    }
    
                    if ($value['parent_id'] == $last_cat_id)
                    {
                        $options[$cat_id]          = $value;
                        $options[$cat_id]['level'] = $level;
                        $options[$cat_id]['id']    = $cat_id;
                        $options[$cat_id]['name']  = $value['cat_name'];
                        unset($arr[$key]);
    
                        if ($value['has_children'] > 0)
                        {
                            if (end($cat_id_array) != $last_cat_id)
                            {
                                $cat_id_array[] = $last_cat_id;
                            }
                            $last_cat_id    = $cat_id;
                            $cat_id_array[] = $cat_id;
                            $level_array[$last_cat_id] = ++$level;
                        }
                    }
                    elseif ($value['parent_id'] > $last_cat_id)
                    {
                        break;
                    }
                }
    
                $count = count($cat_id_array);
                if ($count > 1)
                {
                    $last_cat_id = array_pop($cat_id_array);
                }
                elseif ($count == 1)
                {
                    if ($last_cat_id != end($cat_id_array))
                    {
                        $last_cat_id = end($cat_id_array);
                    }
                    else
                    {
                        $level = 0;
                        $last_cat_id = 0;
                        $cat_id_array = array();
                        continue;
                    }
                }
    
                if ($last_cat_id && isset($level_array[$last_cat_id]))
                {
                    $level = $level_array[$last_cat_id];
                }
                else
                {
                    $level = 0;
                }
            }
            $cat_options[0] = $options;
        }
        else
        {
            $options = $cat_options[0];
        }
    
        if (!$spec_cat_id)
        {
            return $options;
        }
        else
        {
            if (empty($options[$spec_cat_id]))
            {
                return array();
            }
    
            $spec_cat_id_level = $options[$spec_cat_id]['level'];
    
            foreach ($options AS $key => $value)
            {
                if ($key != $spec_cat_id)
                {
                    unset($options[$key]);
                }
                else
                {
                    break;
                }
            }
    
            $spec_cat_id_array = array();
            foreach ($options AS $key => $value)
            {
                if (($spec_cat_id_level == $value['level'] && $value['cat_id'] != $spec_cat_id) ||
                    ($spec_cat_id_level > $value['level']))
                {
                    break;
                }
                else
                {
                    $spec_cat_id_array[$key] = $value;
                }
            }
            $cat_options[$spec_cat_id] = $spec_cat_id_array;
    
            return $spec_cat_id_array;
        }
    }

}