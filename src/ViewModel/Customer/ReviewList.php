<?php
/**
 * Hyvä Themes - https://hyva.io
 * Copyright © Hyvä Themes 2020-present. All rights reserved.
 * This product is licensed per Magento install
 * See https://hyva.io/license
 */

declare(strict_types=1);

namespace Hyva\Theme\ViewModel\Customer;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Review\Model\Review;

class ReviewList implements ArgumentInterface
{
    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    public function __construct(
        CustomerRepositoryInterface $customerRepositoryInterface,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->customerRepository = $customerRepositoryInterface;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * Return map of customer IDs to emails for the given reviews.
     *
     * @param Review[] $reviews
     * @return string[]
     */
    public function getCustomerEmailsForReviews(array $reviews): array
    {
        $customerIds = [];

        foreach ($reviews as $review) {
            if ($customerId = (int) $review->getData('customer_id')) {
                $customerIds[] = $customerId;
            }
        }
        return $this->mapCustomerIdsToEmails($customerIds);
    }

    /**
     * @param int[] $customerIds
     * @return string[]
     */
    private function mapCustomerIdsToEmails(array $customerIds): array
    {
        $this->searchCriteriaBuilder->addFilter('entity_id', $customerIds, 'in');
        $customers = $this->customerRepository->getList($this->searchCriteriaBuilder->create())->getItems();
        return array_reduce($customers, function (array $map, CustomerInterface $customer): array {
            $map[$customer->getId()] = $customer->getEmail();
            return $map;
        }, []);
    }

    /**
     * @return string
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function getCustomerReviewsGraphQlQuery()
    {
        return '
                reviews (
                        pageSize: %pageSize%
                        currentPage: %currentPage%
                    ){
                    items {
                        created_at
                        product {
                            name
                            image {
                                url
                                label
                            }
                            url_key
                        }
                        ratings_breakdown {
                            name
                            value
                        }
                        summary
                        text
                    }
                    page_info {
                        page_size
                        current_page
                        total_pages
                    }
                }
          ';
    }
}
