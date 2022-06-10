import gql from 'graphql-tag'
import {EVENT_FRAGMENT} from './event-fragment';

export const UPDATE_EVENT_MEMO = gql`
    mutation updateEventMemo (
        $id: String!,
        $memo: String!
    ) {
        updateEventMemo (
            id: $id,
            memo: $memo,
        ) {
            ...EventFragment
        }
    }
    ${EVENT_FRAGMENT}
`;
