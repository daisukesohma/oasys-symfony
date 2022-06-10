import gql from 'graphql-tag'
import {USER_FRAGMENT} from './user-fragment';

export const ALL_USERS = gql`
    query allUsers ($search: String, $companyName: String, $types: [String!], $companyId: String, $programId: String, $role: String, $coachId: String, $sortColumn: String, $sortDirection: String, $offset: Int, $limit: Int) {
        allUsers(search: $search, companyName: $companyName, types: $types, companyId: $companyId, programId: $programId, role: $role, coachId: $coachId, sortColumn: $sortColumn, sortDirection: $sortDirection) {
            items(offset: $offset, limit: $limit) {
                ...UserFragment
            },
            count,
        }
    }
    ${USER_FRAGMENT}
`;
