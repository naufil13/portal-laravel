<div class="aside aside-left d-flex aside-fixed" id="kt_aside">

    <!--begin::Secondary-->
    <div class="aside-secondary d-flex flex-row-fluid">
        <!--begin::Workspace-->
        <div class="aside-workspace scroll scroll-push my-2">
            <!--begin::Tab Content-->
            <div class="tab-content">

                <!--begin::Tab Pane-->
                <div class="tab-pane fade show active" id="kt_aside_tab_2">
                    <!--begin::Aside Menu-->
                    <div class="aside-menu-wrapper flex-column-fluid px-3 px-lg-5 py-5" id="kt_aside_menu_wrapper">
                        <!--begin::Menu Container-->
                        <div id="kt_aside_menu" class="aside-menu min-h-lg-800px" data-menu-vertical="1"
                            data-menu-scroll="1">


                            <a href="{{ admin_url('dashboard') }}" style="margin-left:50px">
                                <img alt="Logo" src="{{ asset_url('media/logos/admin_logo.png', 1) }}"
                                    width="100px" style="max-height: auto"/>
                            </a>


                            <!--begin::Menu Nav-->
                            <ul class="menu-nav">



                                @php
                                    $_M = new Multilevel();
                                    $_M->id_Column = 'id';
                                    $_M->title_Column = 'title';
                                    $_M->link_Column = 'module';
                                    $_M->type = 'menu';
                                    $_M->level_spacing = 5;
                                    $_M->selected = $row->parent_id;
                                    $_M->has_child_html = '<i class="menu-arrow"></i>';

                                    $_M->parent_li_start = '	<li class="menu-item menu-item-submenu" aria-haspopup="true"
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    data-menu-toggle="hover">	<a  href="{href}" title="{title}" class="menu-link menu-toggle">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <span class="svg-icon menu-icon">{icon}</span><span class="menu-text">{title}</span>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    {has_child}</a>';

                                    $_M->parent_li_end = '</li>';

                                    $_M->child_ul_start = '<div class="menu-submenu"><i class="menu-arrow"></i><ul class="menu-subnav">';

                                    $_M->child_ul_end = '</ul></div>';

                                    $_M->child_li_start = '	<li class="menu-item {active_class}" aria-haspopup="true">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <a  href="{href}"  class="menu-link" title="{title}">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <i class="menu-bullet menu-bullet-dot mr-4">
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    {icon}
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    </i>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    <span class="menu-text">{title}</span>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    </a>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    </li>';

                                    $_M->child_li_end = '</li>';

                                    $_M->active_class = 'menu-item-active';
                                    $_M->active_link = getUri(2);
                                    $_M->url = admin_url('') . '/';

                                    $_M->query =
                                        "SELECT *,
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        IF(TRIM(module) = '#', 'javascript:;', module) as module,
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        IF(FIND_IN_SET(SUBSTRING_INDEX(image, '.', -1), 'png,jpg,jpeg') > 0,
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        CONCAT('<img width=28 src=\"" .
                                        asset_url('media/icons', true) .
                                        "/', image , '\" alt=\"',title,'\">'),
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        CONCAT('<i class=\"kt-menu__link-icon',icon,'\"></i>')) as icon
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        FROM `modules`
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        WHERE `status`='Active' AND `show_on_menu`=1
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        AND id IN (SELECT `module_id` FROM `user_type_module_rel` WHERE user_type_id='" .
                                        intval(Auth::user()->user_type_id) .
                                        "')
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        ORDER BY ordering ASC";
                                    echo $_M->build();
                                @endphp





                            </ul>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
