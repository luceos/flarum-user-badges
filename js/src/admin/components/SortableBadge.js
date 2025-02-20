import Component from "flarum/Component";
import Button from "flarum/components/Button";
import ConfirmModal from "./ConfirmModal";
import EditBadgeModal from "./EditBadgeModal";

export default class SortableBadge extends Component {
  oninit(attrs) {
    super.oninit(attrs);

    this.loading = false;
  }

  view() {
    const badge = this.attrs.badge;

    if (!badge) return null;

    return (
      <li
        data-id={badge.id()}
        className={!badge.isVisible() ? "BadgeDisabled" : ""}
      >
        <div className="SortableBadges-info">
          <span className={"BadgeDetails"}>
            <span
              className={"UserBadge"}
              onclick={() =>
                app.modal.show(EditBadgeModal, {
                  badge,
                })
              }
            >
              <i className={badge.icon()} /> {badge.name()}
            </span>
          </span>
          <span className={"BadgeButtons"}>
            <Button
              className={"Button"}
              disabled={this.loading}
              onclick={() =>
                app.modal.show(EditBadgeModal, {
                  badge,
                })
              }
            >
              <i className={"fas fa-edit"} />
            </Button>
            <Button
              className={"Button"}
              disabled={this.loading}
              onclick={() =>
                app.modal.show(ConfirmModal, {
                  text: app.translator.trans(
                    "v17development-flarum-badges.admin.confirm_messages.delete_badge"
                  ),
                  promise: true,
                  onconfirm: (resolve, reject) =>
                    badge.delete().then(resolve).catch(reject),
                })
              }
            >
              <i className={"fas fa-trash"} />
            </Button>
          </span>
        </div>
      </li>
    );
  }
}
