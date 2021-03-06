<?php

final class DrydockLeaseListView extends AphrontView {

  private $leases;

  public function setLeases(array $leases) {
    assert_instances_of($leases, 'DrydockLease');
    $this->leases = $leases;
    return $this;
  }

  public function render() {
    $leases = $this->leases;
    $viewer = $this->getUser();

    $view = new PHUIObjectItemListView();

    foreach ($leases as $lease) {
      $item = id(new PHUIObjectItemView())
        ->setUser($viewer)
        ->setHeader($lease->getLeaseName())
        ->setHref('/drydock/lease/'.$lease->getID().'/');

      if ($lease->hasAttachedResource()) {
        $resource = $lease->getResource();

        $resource_href = '/drydock/resource/'.$resource->getID().'/';
        $resource_name = $resource->getName();

        $item->addAttribute(
          phutil_tag(
            'a',
            array(
              'href' => $resource_href,
            ),
            $resource_name));
      }

      $status = DrydockLeaseStatus::getNameForStatus($lease->getStatus());
      $item->addAttribute($status);
      $item->setEpoch($lease->getDateCreated());

      // TODO: Tailor this for clarity.
      if ($lease->isActivating()) {
        $item->setStatusIcon('fa-dot-circle-o yellow');
      } else if ($lease->isActive()) {
        $item->setStatusIcon('fa-dot-circle-o green');
      } else {
        $item->setStatusIcon('fa-dot-circle-o red');
      }

      $view->addItem($item);
    }

    return $view;
  }

}
