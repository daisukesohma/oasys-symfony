import gql from 'graphql-tag'
import {QUESTION_FRAGMENT} from "@/graphql/faq/question-fragment";

export const QUESTION_BY_ID = gql`
    query questionById ($id: String!) {
        questionById (id: $id) {
            ...QuestionFragment
        }
    }
    ${QUESTION_FRAGMENT}
`;