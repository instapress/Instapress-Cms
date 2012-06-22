<?php

	/**
	 * Singular Category class.
	 *
	 * @author Rashid Moahamd <rashid.mohamad@instamedia.com>
	 * @version 1.0
	 * @copyright Instamedia
	 */
	class Cms_Cluster extends Cms_AbstractSingular {

		protected $_foreignKey = 'clusterId';
		protected $_dbClass = 'Cluster';
	}
