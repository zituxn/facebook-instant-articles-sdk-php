<?hh // strict
/**
 * Copyright (c) 2016-present, Facebook, Inc.
 * All rights reserved.
 *
 * This source code is licensed under the license found in the
 * LICENSE file in the root directory of this source tree.
 */
 namespace Facebook\InstantArticles\Elements;

 /**
  * Interface ChildrenContainer
  * This interface specifies the navigatable objects that have children.
  */
interface ChildrenContainer
{

    /**
     * Must return an vec of Element typed objects.
     * To navigate thru the ChildrenContainer object tree, always check if it is a ChildrenContainer.
     * <code>
     *     if (Type::is($object, ChildrenContainer::getClassName())) {
     *         foreach($object->getChildren() as $child) {
     *              //$child operations
     *         }
     *     }
     * </code>
     *
     * @return vec<Element> All implementing classes returns an vec of Element.
     */
    public function getContainerChildren(): vec<Element>;

    /**
     * Auxiliary method to extract all Elements full qualified class name.
     *
     * @return string The full qualified name of class
     */
    public static function getClassName(): string;
}
