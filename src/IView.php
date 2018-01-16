<?php
/**
 * @link https://github.com/antkaz/yii2-iview
 * @copyright Copyright (c) 2018 Anton Kazarinov
 * @license https://github.com/antkaz/yii2-iview/blob/master/LICENSE
 */

namespace antkaz\iview;


use yii;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\View;
use antkaz\iview\assets\IViewAsset;

/**
 * Render an IView components
 *
 * For example,
 *
 * ```php
 * echo IView::begin([
 *     'clientOptions' => [
 *         'data' => [
 *             'visible' => false
 *         ],
 *         'methods' => [
 *             'show' => new \yii\web\JsExpression('function() {this.visible = true;}')
 *         ]
 *     ]
 * ]); ?>
 *
 * <i-button @click="show">Click me!</i-button>
 * <Modal v-model="visible" title="Welcome">Welcome to iView</Modal>
 *
 * <?php IView::end() ?>
 * ```
 *
 * @author Anton Kazarinov <askazarinov@gmail.com>
 * @package antkaz\iview
 */
class IView extends yii\base\Widget
{
    /**
     * @var array The HTML tag attributes for the widget container tag
     *
     * @see \yii\helpers\Html::renderTagAttributes() for details on how attributes are being rendered.
     */
    public $options;

    /**
     * @var array The options for the Vue.
     *
     * @see https://vuejs.org/v2/api/#Options-Data for informations about the supported options
     */
    public $clientOptions;

    /**
     * @var string
     */
    public $language;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        if (!isset($this->options['id'])) {
            $this->options['id'] = $this->getId();
        }

        ob_start();
        ob_implicit_flush(false);
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        $this->registerJs();

        $content = ob_get_clean();

        echo Html::beginTag('div', $this->options);
        echo $content;
        echo Html::endTag('div');
    }

    /**
     * Registers a specific asset bundles
     */
    protected function registerJs()
    {
        $this->registerIVIew();
        $this->registerVue($this->getId());
    }

    /**
     * Registers a specific IView library asset bundle, initializes language
     */
    private function registerIVIew()
    {
        $view = $this->getView();

        $language = empty($this->language) ? Yii::$app->language : $this->language;

        $iview = IViewAsset::register($view);
        $iview->language = $language;

        $view->registerJs("iview.lang('" . $language . "')", View::POS_HEAD);
    }

    /**
     * Registers a specific VueJS framework asset bundle
     *
     * @param string $id The ID of the widget container tag
     */
    private function registerVue($id)
    {
        $this->clientOptions['el'] = '#' . $id;
        $options = Json::htmlEncode($this->clientOptions);
        $js = "var app = new Vue({$options});";
        $this->getView()->registerJs($js, View::POS_END);
    }
}