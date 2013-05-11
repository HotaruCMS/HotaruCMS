Views and Elements
==================

Render an element (re-usable sub-view):

	<?php $this->renderElement('foo'); ?>

Render an element with data:

	<?php $this->renderElement('foo', array('varName' => 'value')); ?>

Setting Layout Variables
------------------------

In the view/element:

	$this->setLayoutVar('layoutVarName', 'value');

In the layout:

	<?php echo $layoutVarName ?>
