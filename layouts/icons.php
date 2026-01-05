<?php
function icon_svg(string $name): string
{
  switch ($name) {
    case "dashboard":
      return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7" rx="2"></rect><rect x="14" y="3" width="7" height="7" rx="2"></rect><rect x="14" y="14" width="7" height="7" rx="2"></rect><rect x="3" y="14" width="7" height="7" rx="2"></rect></svg>';
    case "alternatives":
      return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 21s7-7 7-11a7 7 0 0 0-14 0c0 4 7 11 7 11z"></path><circle cx="12" cy="10" r="2.5"></circle></svg>';
    case "criteria":
      return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><line x1="4" y1="6" x2="20" y2="6"></line><circle cx="9" cy="6" r="2"></circle><line x1="4" y1="12" x2="20" y2="12"></line><circle cx="15" cy="12" r="2"></circle><line x1="4" y1="18" x2="20" y2="18"></line><circle cx="7" cy="18" r="2"></circle></svg>';
    case "weights":
      return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="3" x2="12" y2="21"></line><line x1="6" y1="7" x2="18" y2="7"></line><path d="M6 7l-4 7h8l-4-7z"></path><path d="M18 7l-4 7h8l-4-7z"></path></svg>';
    case "evaluations":
      return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="6" y="4" width="12" height="16" rx="2"></rect><path d="M9 4V2h6v2"></path><path d="M9 12l2 2 4-4"></path></svg>';
    case "calculate":
      return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="5" y="3" width="14" height="18" rx="2"></rect><line x1="8" y1="7" x2="16" y2="7"></line><rect x="8" y="11" width="3" height="3" rx="1"></rect><rect x="13" y="11" width="3" height="3" rx="1"></rect><rect x="8" y="16" width="3" height="3" rx="1"></rect><rect x="13" y="16" width="3" height="3" rx="1"></rect></svg>';
    case "result":
      return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M8 21h8"></path><path d="M12 17v4"></path><path d="M7 4h10v3a5 5 0 0 1-10 0V4z"></path><path d="M5 5H3v2a4 4 0 0 0 4 4"></path><path d="M19 5h2v2a4 4 0 0 1-4 4"></path></svg>';
    case "details":
      return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><line x1="8" y1="7" x2="20" y2="7"></line><line x1="8" y1="12" x2="20" y2="12"></line><line x1="8" y1="17" x2="20" y2="17"></line><circle cx="4" cy="7" r="1.5"></circle><circle cx="4" cy="12" r="1.5"></circle><circle cx="4" cy="17" r="1.5"></circle></svg>';
    case "star":
      return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3l2.7 5.3 5.9.9-4.3 4.2 1 6-5.3-2.8-5.3 2.8 1-6-4.3-4.2 5.9-.9L12 3z"></path></svg>';
  }
  return "";
}
