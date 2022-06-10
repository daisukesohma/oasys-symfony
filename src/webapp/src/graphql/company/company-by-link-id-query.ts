import gql from 'graphql-tag'
import {COMPANY_FRAGMENT} from './company-fragment';

export const COMPANY_BY_LINK_ID = gql`
    query companyByLinkId ($linkId: String!) {
        companyByLinkId (linkId: $linkId) {
            id
            name
            functions
            services
        }
    }
`;