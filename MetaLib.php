<?php
	/**
	 * Created by tokapps TM.
	 * Programmer: gholamreza beheshtian
	 * Mobile:09353466620
	 * Company Phone:05138846411
	 * Site:http://tokapps.ir
	 * Date: ۰۲/۰۹/۲۰۲۰
	 * Time: ۱۷:۱۷ بعدازظهر
	 */
	
	namespace system\lib;
	
	
	use system\modules\metadata\models\Metadata;
	use system\modules\pages\models\Pages;
	
	class MetaLib {
		
		
		/**
		 * @param              $meta_key
		 * @param int          $module_id
		 * @param string|array $value
		 * @param boolean      $update
		 *
		 * @return boolean
		 */
		public function set( $meta_key , $value , $module_id = 0 , $update = false ) {
			if ( $update ) {
				$meta = $this->get( $meta_key , $module_id );
				
				if ( ! empty( $meta ) ) {
					$meta->delete();
				}
				
				return $this->set( $meta_key , $value , $module_id , $update = false );
				
				
			} else {
				if ( is_array( $value ) || is_object( $value ) ) {
					$meta             = new Metadata();
					$meta->meta_key   = $meta_key;
					$meta->content    = json_encode( $value );
					$meta->module_id  = $module_id;
					$meta->updated_at = date( 'Y-m-d H:i:s' );
					if ( is_array( $value ) ) {
						$meta->type = Metadata::TYPE_ARRAY;
					}
					if ( is_object( $value ) ) {
						$meta->type = Metadata::TYPE_OBJECT;
					}
					$save = $meta->save();
					if ( ! $save ) {
						self::set( 'metaError' , $meta->errors , 0 );
					}
					
					return $save;
				} else {
					$meta             = new Metadata();
					$meta->meta_key   = $meta_key;
					$meta->content    = $value;
					$meta->updated_at = date( 'Y-m-d H:i:s' );
					$meta->module_id  = $module_id;
					$meta->type       = Metadata::TYPE_SINGLE;
					$save             = $meta->save();
					if ( ! $save ) {
						self::set( 'metaError' , $meta->errors , 0 );
					}
					
					return $save;
				}
			}
		}
		
		/**
		 * @param $meta_key
		 * @param $module_id
		 *
		 * @return \system\modules\metadata\models\Metadata|null
		 */
		public function get( $meta_key , $module_id ) {
			$model = Metadata::find()->where( [ 'module_id' => $module_id , 'meta_key' => $meta_key ] )->one();
			if ( ! empty( $model ) ) {
				switch ( $model->type ) {
					case $model::TYPE_ARRAY:
					case $model::TYPE_OBJECT:
						$model->content = json_decode( $model->content );
				}
				
				return $model;
			}
		}
		
		public function get_Value( $meta_key , $value ) {
			return Metadata::find()->where( [ 'content' => $value , 'meta_key' => $meta_key ] )->all();
		}
		
		/**
		 * @param      $module_id
		 * @param null $meta_key
		 *
		 * @throws \Throwable
		 * @throws \yii\base\InvalidConfigException
		 * @throws \yii\db\StaleObjectException
		 */
		public function delete( $module_id , $meta_key = null ) {
			if ( empty( $meta_key ) ) {
				$models = Metadata::find()->where( [ 'module_id' => $module_id ] )->all();
				if ( ! empty( $models ) ) {
					foreach ( $models as $model ) {
						$model->delete();
					}
				}
			} else {
				$model = Metadata::find()->where( [ 'module_id' => $module_id , 'meta_key' => $meta_key ] )->one();
				if ( ! empty( $model ) ) {
					$model->delete();
				}
			}
			
		}
	}
