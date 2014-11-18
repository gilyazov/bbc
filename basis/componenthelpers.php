<?php
/**
 * Basis components
 *
 * @package components
 * @subpackage basis
 * @author Nik Samokhvalov <nik@samokhvalov.info>
 * @copyright Copyright (c) 2014, Nik Samokhvalov
 */
namespace Components\Basis;

use Bitrix\Main;


if(!defined('B_PROLOG_INCLUDED')||B_PROLOG_INCLUDED!==true)die();


/**
 * Helpers for working with components
 */
class ComponentHelpers
{
    /**
     * Include modules
     *
     * @param array $needModules Array with codes of the modules
     * @throws \Bitrix\Main\LoaderException
     */
    public static function includeModules($needModules = array())
    {
        foreach ($needModules as $module)
        {
            if (!Main\Loader::includeModule($module))
            {
                throw new Main\LoaderException('Failed include module "'.$module.'"');
            }
        }
    }

    /**
     * Prepare and returns parameters of the component
     *
     * @param string $component Component name. For example: basis:elements.list
     * @param array $prepareParams Array with settings for prepare parameters of merged the component. For example:
     *                                      <code>
     *                                      array(
     *                                          'SELECT_FIELDS' => array(
     *                                              'RENAME' => 'LIST_SELECT_FIELDS',
     *                                              'MOVE' => 'LIST'
     *                                      )
     *                                      </code>
     *                                      Options:
     *                                      <ul>
     *                                      <li> RENAME — rename parameter
     *                                      <li> MOVE — move parameter to another parameter group
     *                                      <li> DELETE — true or false
     *                                      </ul>
     * @param array $arCurrentValues Don't change the name! It's used in the .parameters.php file (Hello from Bitrix)
     * @return array Array for use in variable $arComponentParameters in the .parameters.php
     * @throws \Bitrix\Main\LoaderException
     */
    public static function getParameters($component, $prepareParams = array(), array $arCurrentValues)
    {
        $componentParams = \CComponentUtil::GetComponentProps($component, $arCurrentValues);

        if ($componentParams === false)
        {
            throw new Main\LoaderException('Failed loading parameters for '.$component);
        }

        if (!empty($prepareParams))
        {
            foreach ($componentParams['PARAMETERS'] as $code => &$params)
            {
                if ($prepareParams[$code]['DELETE'])
                {
                    unset($componentParams['PARAMETERS'][$code]);
                    continue;
                }

                if ($prepareParams[$code]['MOVE'])
                {
                    $params['PARENT'] = $prepareParams[$code]['MOVE'];
                }

                if ($prepareParams[$code]['RENAME'])
                {
                    $componentParams['PARAMETERS'][$prepareParams[$code]['RENAME']] = $params;

                    unset($componentParams['PARAMETERS'][$code]);
                }
            }

            unset($params);
        }

        return $componentParams;
    }
}