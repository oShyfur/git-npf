<?php

namespace Np\Structure\Classes;

use Backend\Models\User as BackendUserModel;
use Np\Structure\Models\Site;
use Illuminate\Support\Facades\DB;
use Backend\Controllers\Users as BackendUsersController;
use Backend\Facades\BackendAuth;
use Backend\Models\UserRole;
use October\Rain\Exception\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\URL;

use Event, View;
use Log;
use Np\Structure\Facades\Oisf;

/**
 * Class BackendUserExtension
 * @package Renatio\Logout\Classes
 */
class BackendUserExtension
{
    /**
     * @return void
     */
    public function boot()
    {
        $this->disableGroup();
        $this->extendModel();
        $this->extendController();
        $this->extendForSso();
    }

    public function disableGroup()
    {
        BackendUsersController::extendListColumns(function ($list, $model) {
            $list->removeColumn('groups');
        });

        BackendUsersController::extendFormFields(function ($form, $model, $context) {
            $form->removeField('groups');
        });
    }


    public function extendForSso()
    {
        BackendUserModel::extend(function ($model) {

            //oisf related functions

            $model->addDynamicMethod('getMinistryOptions', function () use ($model) {
                $items = [];

                if (!empty($model->is_sso)) {
                    $items = collect(Oisf::getOisfOfficeMinistry())->pluck('name', 'id')->toArray();
                }
                return $items;
            });

            $model->addDynamicMethod('getLayerOptions', function () use ($model) {
                $items = [];

                if (isset($model->_ministry))
                    $items = collect(Oisf::getOisfOfficeLayer($model->_ministry))->pluck('name', 'id')->toArray();

                return $items;
            });

            $model->addDynamicMethod('getOfficeOptions', function () use ($model) {

                $items = [];

                if ($model->_layer)
                    $items = collect(Oisf::getOisfOffice($model->_ministry, $model->_layer))->pluck('name', 'id')->toArray();

                return $items;
            });

            $model->addDynamicMethod('getEmployeeOptions', function () use ($model) {

                $items = [];

                if ($model->_office) {

                    $empployees = collect(Oisf::getOisfEmployeeOffice($model->_office))->toArray();

                    foreach ($empployees as $emp) {


                        $employee = Oisf::getOisfEmployee($emp['employeeRecord']);
                        $employee = $employee[0];

                        if (isset($employee)) {

                            $item['id'] = isset($employee['id']) ? $employee['id'] . '-' . $emp['designation'] . '-' . $emp['idNumber'] : 0;
                            $item['name'] = isset($employee['name']) ? $employee['name'] : 0;

                            $items[] = $item;
                        }
                    }

                    $items = collect($items)->pluck('name', 'id')->toArray();
                }

                return $items;
            });
        });
    }


    public function extendController()
    {
        BackendUsersController::extendListFilterScopes(function ($filter) {

            if (!BackendAuth::getUser()->adminLevelUser() && !BackendAuth::getUser()->isSiteAdmin())
                $filter->removeScope('sites');
        });

        BackendUsersController::extendFormFields(function ($form, $model, $context) {
            if (!$model instanceof BackendUserModel) {
                return;
            }
            if (!BackendAuth::getUser()->adminLevelUser())
                $form->removeField('is_admin');
        });


        BackendUsersController::extend(function ($controller) {

            //add js for sso

            $controller->addDynamicMethod('onGetOisfEmployee', function () {

                list($id, $designation, $idNumber) = explode('-', post('employee_id'));

                $employee = Oisf::getOisfEmployee($id);
                $employee = $employee[0];
                $employee['designation'] = $designation;

                //return data
                $data['login'] = $idNumber;
                $data['email'] = $employee['email'];
                $data['first_name'] = $employee['name'] ?: $employee['nameBn'];
                $data['designation'] = $designation;
                $data['phone'] = $employee['mobile'];

                return [
                    'employee' => $data
                ];
            });


            $controller->formConfig = '';
            $myFormConfigPath = '$/np/structure/controllers/user/config_form.yaml';
            $listConfigPath = '$/np/structure/controllers/user/config_list.yaml';


            $controller->formConfig = $controller->mergeConfig(
                $controller->formConfig,
                $myFormConfigPath
            );

            $controller->listConfig = $controller->mergeConfig(
                $controller->listConfig,
                $listConfigPath
            );
        });
    }
    /**
     * @return void
     */

    protected function extendModel()
    {


        BackendUserModel::extend(function ($model) {

            $model->bindEvent('model.afterCreate', function () use ($model) {

                $currentSiteId = NP::getSiteId();
                if (!BackendAuth::getUser()->adminLevelUser() and $currentSiteId) {
                    Site::find($currentSiteId)->users()->attach($model);
                }
            });

            $model->bindEvent('model.beforeSave', function () use ($model) {
                // for security purpose
                if (!array_key_exists($model->role->id, $model->getUserRoles()->toArray()))
                    throw new ValidationException(['role' => 'You can not assign role not showing in list']);
            });

            $model->belongsToMany['sites'] = [
                'Np\Structure\Models\Site',
                'table' => 'np_structure_site_user'
            ];

            //update avatar relation
            $model->attachOne['avatar'] = 'Np\Structure\Models\File';

            //user edit content type list tab
            $model->addJsonable('allowed_ct');
            $model->addDynamicMethod('getContentTypes', function () use ($model) {

                $list = [];

                $ctList = session('site.resources.content_types', $list);

                foreach ($ctList as $ct)
                    $list[$ct['id']] = $ct['name'];

                return $list;
            });


            $model->addDynamicMethod('getUserRoles', function () use ($model) {

                $roles = UserRole::query();
                $roles->when(!$model->adminLevelUser(), function ($q) {
                    return $q->whereNotIn('code', NP::adminLevelRole());
                });

                $roles = $roles->pluck('name', 'id');
                return $roles;
            });

            $model->addDynamicMethod('getDefaultSite', function () use ($model) {

                return $model->sites()->wherePivot('default', '=', 1);
            });


            $model->addDynamicMethod('adminLevelUser', function () use ($model) {

                return ($model->is_superuser or $model->is_admin);
            });

            $model->addDynamicMethod('isSiteAdmin', function () use ($model) {

                return !$model->is_superuser && $model->role->code == 'site-admin';
            });

            $model->addDynamicMethod('getSiteList', function () use ($model) {
                if (!$model->adminLevelUser()) {
                    return $model->sites;
                }
                // echo URL::current();
                if(URL::current()=='https://login.dhaka.gov.bd/backend' || URL::current()=='https://login.dhaka.gov.bd/backend/backend'){
                    return Site::where('db_id', '=', '9bdf8720-f234-4121-b3f6-795948ad9e33')->get();
                }elseif(URL::current()=='https://login.sylhet.gov.bd/backend' || URL::current()=='https://login.sylhet.gov.bd/backend/backend'){
                    return Site::where('db_id', '=', '5b8641e5-db8e-4372-a165-bec7b16d28c8')->get();
                }elseif(URL::current()=='https://login.rajshahi.gov.bd/backend' || URL::current()=='https://login.rajshahi.gov.bd/backend/backend'){
                    return Site::where('db_id', '=', 'a0e29d09-395c-49d5-9d4e-d23ba3e63943')->get();
                }elseif(URL::current()=='https://login.mymensingh.gov.bd/backend' || URL::current()=='https://login.mymensingh.gov.bd/backend/backend'){
                    return Site::where('db_id', '=', '6a1ce5d7-c137-4fdc-9fb8-85c0fbd2ddfa')->get();
                }elseif(URL::current()=='https://login.barisal.gov.bd/backend' || URL::current()=='https://login.barisal.gov.bd/backend/backend'){
                    return Site::where('db_id', '=', '01f524cd-7b01-42ef-a4a2-d9c187ed0401')->get();
                }elseif(URL::current()=='https://login.chittagong.gov.bd/backend' || URL::current()=='https://login.chittagong.gov.bd/backend/backend'){
                    return Site::where('db_id', '=', '62cdc646-a6cc-45f3-8713-9799b36c396a')->get();
                }elseif(URL::current()=='https://login.rangpur.gov.bd/backend' || URL::current()=='https://login.rangpur.gov.bd/backend/backend'){
                    return Site::where('db_id', '=', '6c9aebeb-269a-4ebf-83da-266bce2483b6')->get();
                }elseif(URL::current()=='https://login.khulna.gov.bd/backend' || URL::current()=='https://login.khulna.gov.bd/backend/backend'){
                    return Site::where('db_id', '=', '4b63fde4-97ff-482f-b164-3e1fbd6e8e4d')->get();
                }elseif(URL::current()=='http://login.a2i.gov.bd/backend' || URL::current()=='http://login.a2i.gov.bd/backend/backend'){
                    return Site::where('db_id', '=', 'b630818e-d23c-48de-8673-877cc7e5dce5')->get();
                }elseif(URL::current()=='http://stage-login.portal.gov.bd/backend' || URL::current()=='http://stage-login.portal.gov.bd/backend/backend'){
                    return Site::where('db_id', '=', '4b63fde4-97ff-482f-b164-3e1fbd6e8e4d')->get();
                    // return Site::all();
                }else{
                    // return Site::where('id', '=', '52864')->get();
                    return Site::paginate(50);
                    // return Site::all();
                }
            });
        });
    }

    public function userListQueryExtend($widget, $query)
    {
        if ($widget->model instanceof BackendUserModel) {

            $noSiteFilter = true;
            if (request('scopeName') == 'sites' && isset(request('options')['active']))
                $noSiteFilter = false;


            $user = BackendAuth::getUser();
            if (!$user->adminLevelUser() && !$user->isSiteAdmin()) {
                $query->whereHas('sites', function ($q) use ($user) {
                    $q->whereHas('users', function ($q) use ($user) {
                        $q->where('id', $user->id);
                    });
                });
            } elseif ($user->isSiteAdmin() && $noSiteFilter) {
                $query->whereHas('sites', function ($q) use ($user) {
                    $q->whereHas('users', function ($q) use ($user) {
                        $q->where('id', $user->id);
                    });
                });
            }
        }
    }
}
